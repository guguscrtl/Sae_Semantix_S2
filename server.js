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

      const words = data.split('\n').filter(word => !hasAccents(word));

      if (words.length === 0) {
        reject('No words without accents found.');
        return;
      }

      const randomWord = words[Math.floor(Math.random() * words.length)];
      resolve(randomWord);
    });
  });
}

function hasAccents(word) {
  return /[áàâäãåçéèêëíìîïñóòôöõúùûüýÿ]/i.test(word);
}

function sleep(ms) {
  return new Promise(resolve => setTimeout(resolve, ms));
}

async function getWord() {
  await sleep(1000);
  const word = await getRandomWordFromFile('output.txt');
  return word;
}

function runCommandCreateGame(game_id, socket_id, randomWord, randomWord2) {
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
    return {randomWord, randomWord2};
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
let mot1;
let mot2;

io.on('connection', (socket) => {
  console.log('a user connected');

  socket.on('createGame', async (username) => {
    const gameId = generateUniqueGameId();
    try {
      const [mot1, mot2] = await Promise.all([getWord(), getWord()]);
      console.log(mot1);
      console.log(mot2);
      const newNode1 = { id: 0, label: mot1 };
      const newNode2 = { id: 1, label: mot2 };
      games[gameId] = {
        players: [username],
        currentTurn: 0,
        score: 0,
        timer: null, 
        nodes: [newNode1, newNode2], 
        edges: { from: newNode1.id, to: newNode2.id }
      };
      const game = games[gameId];
      socket.join(gameId);
      socket.emit('gameCreated', gameId, username);
      startTimer(gameId, 30 * 1000);
      console.log(socket.id);
      console.log("gameId cree : " + gameId);
      runCommandCreateGame(gameId, socket.id, mot1, mot2);
      socket.emit('setGame', gameId);
      io.to(socket.id).emit('your turn');
      io.to(socket.id).emit('update score', game.score);
    } catch (error) {
      console.error('Error creating game:', error);
      socket.emit('error', 'Failed to create game');
    }
  });

  socket.on('joinGame', (gameId, username) => {
    const game = games[gameId];
    if (game) {
      game.players.push(username);
      socket.join(gameId);
      io.in(gameId).emit('joinedGame', gameId, game.players, game.nodes, game.edges);
      console.log(socket.id);
      io.to(socket.id).emit('update score', game.score);
      if (game.players.length === 1) {
        io.to(game.players[game.currentTurn]).emit('your turn');
      }
      if (game.timer) {
        const remainingTime = game.timer.duration - (Date.now() - game.timer.startTime);
        socket.emit('startTimer', { duration: remainingTime, startTime: Date.now() });
      }
    } else {
      socket.emit('error', 'Game not found');
    }
  });

  const startTimer = (gameId, duration) => {
    const startTime = Date.now();
    games[gameId].timer = { duration, startTime };
    io.to(gameId).emit('startTimer', { duration, startTime });

    const interval = setInterval(() => {
      const remainingTime = duration - (Date.now() - startTime);
      if (remainingTime <= 0) {
        clearInterval(interval);
        handleTimerEnd(gameId);
      } else {
        io.to(gameId).emit('timerUpdate', { remainingTime });
      }
    }, 1000);
  };

  const handleTimerEnd = (gameId) => {
    io.to(gameId).emit('timerEnd');
    const game = games[gameId];
    if (game) {
      if (game.timer.duration === 30 * 1000) {
        startTimer(gameId, 120 * 1000);
      } else {
        io.to(gameId).emit('gameFinish');
      }
    }
  };

  socket.on('new word', async (word) => {
    console.log("Le mot tapé est: " + word);
    console.log("Salles du socket avant de trouver gameId: ", Array.from(socket.rooms));
    const gameId = Array.from(socket.rooms).find(r => r !== socket.id);
    console.log('gameId trouvé: ' + gameId);
    if (!gameId) return;

    const game = games[gameId];
    const playerIndex = game.players.indexOf(socket.id);
    if (playerIndex !== game.currentTurn) {
      socket.emit('not your turn');
      return;
    }

    try {
      const response = await axios.get(`http://localhost/Sae_Semantix_S2/execute_exe.php?exe=C\\addword.exe&param_s=static_tree.lex&param_f=${gameId}&param_w=${word}`);
      if (response.status === 200) {
        console.log('Response OK de l\'addword_exe');
      } else {
        console.error('Erreur lors de l\'exécution de l\'addword_exe');
        return;
      }
    } catch (error) {
      console.error('Erreur lors de la requête HTTP (addword_exe):', error);
      return;
    }

    try {
      game.currentTurn = (game.currentTurn + 1) % game.players.length;
      io.to(game.players[game.currentTurn]).emit('your turn');
      // Fetch network structure from PHP script
      const networkResponse = await axios.get(`http://localhost/Sae_Semantix_S2/optim_tree.php?gameId=${gameId}`);
      if (networkResponse.status === 200) {
        const { nodes, edges } = networkResponse.data;
        console.log("La structure a été envoyée à : " + gameId);
        try {
          const response = await axios.get(`http://localhost/Sae_Semantix_S2/score_java.php?gameId=${gameId}`);
          if (response.status === 200) {
            game.score = parseInt(response.data.score);
            console.log('Score : ' + game.score);
            console.log('out: ' + response.data.out);
            console.log('Response : ' + JSON.stringify(response.data));
            io.in(gameId).emit('update score', game.score);
          } else {
            console.error('Erreur lors de la récupération du score');
            return;
          }
        } catch (error) {
          console.error('Erreur lors de la requête HTTP (score_java):', error);
          return;
        }
        // Emit the new network structure to the client
        io.in(gameId).emit('update network', { nodes, edges });
      } else {
        console.error('Erreur lors de la récupération de la structure du réseau');
        return;
      }
    } catch (error) {
      console.error('Erreur lors de la requête HTTP (optim_tree):', error);
      return;
    }
    console.log('turn1: ' + game.currentTurn);
    console.log(game.score);
    console.log("Game ID: " + gameId);
    console.log('nouveau score :' + game.score);
    console.log('turn2: ' + game.currentTurn);
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
