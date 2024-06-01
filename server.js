const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const axios = require('axios');
const { exec } = require('child_process');
const fs = require('fs');



function runCommandCreateDictionnary() {
  const command = `C\\build_lex_index.exe C\\word2vec.bin`;
  exec(command, (error, stdout, stderr) => {
    if (error) {
      console.error(`Erreur lors de l'exécution de la commande: ${error.message}`);
      return;
    }
    if (stderr) {
      console.error(`Erreur standard: ${stderr}`);
      return;
    }
    console.log(`Sortie standard ok pour dico: ${stdout}`);
  });
}

// Fonction pour obtenir un mot aléatoire depuis un fichier texte
function getRandomWordFromFile(filePath) {
  return new Promise((resolve, reject) => {
    fs.readFile(filePath, 'utf8', (err, data) => {
      if (err) {
        reject(err);
        return;
      }
      const words = data.split('\n');
      const randomWord = words[Math.floor(Math.random() * words.length)];
      resolve(randomWord);
    });
  });
}

function runCommandCreateGame(game_id) {
  Promise.all([
    getRandomWordFromFile('output.txt'),
    getRandomWordFromFile('output.txt')
  ]).then(([randomWord, randomWord2]) => {
    const command = `C\\new_game static_tree.lex ${game_id} ${randomWord} ${randomWord2}`;
    exec(command, (error, stdout, stderr) => {
      if (error) {
        console.error(`Erreur lors de l'exécution de la commande: ${error.message}`);
        return;
      }
      if (stderr) {
        console.error(`Erreur standard: ${stderr}`);
        return;
      }
      console.log(`Sortie standard ok pour creategame: ${stdout}`);

      // Emit the words to the client
      io.to(game_id).emit('gameCreated', { id: game_id, words: [randomWord, randomWord2] });
    });
  }).catch(error => {
    console.error(`Erreur lors du choix des mots: ${error.message}`);
  });
}

const app = express();
const server = http.createServer(app);
runCommandCreateDictionnary();
const io = new Server(server, {
  cors: {
    origin: "http://localhost:3000",
    methods: ["GET", "POST"]
  }
});

const games = {};

io.on('connection', (socket) => {
  console.log('a user connected');

  socket.on('createGame', () => {
    const gameId = generateUniqueGameId();
    games[gameId] = {
      players: [socket.id],
      currentTurn: 0,
      score: 0
    };
    socket.join(gameId);
    socket.emit('gameCreated', gameId);
    runCommandCreateGame(gameId);
    io.to(socket.id).emit('your turn');
    io.to(socket.id).emit('update score', 0);
  });

  socket.on('joinGame', (gameId) => {
    const game = games[gameId];
    if (game) {
      game.players.push(socket.id);
      socket.join(gameId);
      socket.emit('joinedGame', gameId);
      io.to(socket.id).emit('update score', game.score);
      if (game.players.length === 1) {
        io.to(game.players[game.currentTurn]).emit('your turn');
      }
    } else {
      socket.emit('error', 'Game not found');
    }
  });

  socket.on('new word', async (word) => {
    console.log("le mot tapé est: " + word);
    const gameId = Array.from(socket.rooms).find(r => r !== socket.id);
    if (!gameId) return;

    const game = games[gameId];
    const playerIndex = game.players.indexOf(socket.id);
    if (playerIndex !== game.currentTurn) {
      socket.emit('not your turn');
      return;
    }

    try {
      const response = await axios.get(`http://localhost/my-app/execute_exe.php?exe=C\\addword.exe&param_s=static_tree.lex&param_f=${gameId}&param_w=${word}`);
      if (response.status === 200) {
        console.log('Response OK de l\'addword_exe');
      } else {
        console.error('Erreur lors de la récupération du score');
        return;
      }
    } catch (error) {
      console.error('Erreur lors de la requête HTTP:', error);
      return;
    }

    try {
      const response = await axios.get(`http://localhost/my-app/get_score.php?file=./save/${gameId}.txt&word=` + word);
      //const response = await axios.get('http://localhost/my-app/get_score.php');
      if (response.status === 200) {
        game.score = response.data.score;
        console.log('Score : ' + game.score);
        console.log('Response : ' + JSON.stringify(response.data));
      } else {
        console.error('Erreur lors de la récupération du score');
        return;
      }
    } catch (error) {
      console.error('Erreur lors de la requête HTTP:', error);
      return;
    }

    game.currentTurn = (game.currentTurn + 1) % game.players.length;

    io.to(game.players[game.currentTurn]).emit('your turn');
    io.in(gameId).emit('new word', { word, id: socket.id });
    io.in(gameId).emit('update score', game.score);
    console.log('nouveau score :' + game.score);
  });

  socket.on('chat message', (msg) => {
    const gameId = Array.from(socket.rooms).find(r => r !== socket.id);
    if (gameId) {
      io.in(gameId).emit('chat message', msg);
    }
  });

  socket.on('disconnect', () => {
    console.log('user disconnected');
    for (const [gameId, game] of Object.entries(games)) {
      const playerIndex = game.players.indexOf(socket.id);
      if (playerIndex !== -1) {
        game.players.splice(playerIndex, 1);
        if (game.players.length === 0) {
          delete games[gameId];
        } else if (playerIndex === game.currentTurn) {
          game.currentTurn = game.currentTurn % game.players.length;
          io.to(game.players[game.currentTurn]).emit('your turn');
        }
        break;
      }
    }
  });
});

function generateUniqueGameId() {
  return Math.random().toString(36).substr(2, 9);
}

server.listen(3001, () => {
  console.log('listening on *:3001');
});
