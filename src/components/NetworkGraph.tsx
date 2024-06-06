import React, { useEffect, useRef, useState } from 'react';
import { DataSet, Network } from 'vis-network/standalone/esm/vis-network';
import { io, Socket } from 'socket.io-client';
import 'vis-network/styles/vis-network.css';
import Timer, { Props } from './Timer';
import axios from 'axios';

const SOCKET_SERVER_URL = 'http://localhost:3001';

const NetworkGraph: React.FC = () => {
  const networkContainer = useRef<HTMLDivElement | null>(null);
  const timerRef = useRef<Timer>(null);
  const [network, setNetwork] = useState<Network | null>(null);
  const [nodes, setNodes] = useState<DataSet<any>>(new DataSet([]));
  const [edges, setEdges] = useState<DataSet<any>>(new DataSet([]));
  const [socket, setSocket] = useState<Socket | null>(null);
  const [word, setWord] = useState<string>('');
  const [isMyTurn, setIsMyTurn] = useState<boolean>(false);
  const [score, setScore] = useState<number>(0);
  const [messages, setMessages] = useState<Array<{ id: string, message: string }>>([]);
  const [players, setPlayers] = useState<Array<{ pseudo: string }>>([]);
  const [newMessage, setNewMessage] = useState<string>('');
  const [gameId, setGameId] = useState<string | null>(null);
  const [liste_player, setListPlayer] = useState<string[]>([]);
  const [isStart, setStart] = useState<boolean>(false);
  const [isFinish, setFinish] = useState<boolean>(false);
  const [isEnabled, setInput] = useState<boolean>(false);
  const [isOwner, setOwner] = useState<boolean>(false);
  const [isSolo, setSolo] = useState<boolean>(false);
  const [isJoiner, setJoiner] = useState<boolean>(false);
  const [timerProps, setTimerProps] = useState<Props>({
    seconds: 120,
    size: 70,
    strokeBgColor: 'lightgray',
    strokeColor: 'lightblue',
    strokeWidth: 10,
    onTimerEnd: () => setFinish(true),
  });

  const handleTimerEnd = () => {
    setFinish(true);
  };
  
  const [pseudo, setNewPseudo] = useState<string>('');

  const PseudoList: React.FC = () => {
    const pseudoArray = pseudo.split(',');
    return (
      <div>
          {pseudoArray.map((item, index) => (
            <li key={index}>{item}</li>
          ))}
      </div>
    );
  };

  const refreshPage = () => {
    window.location.reload();
  };

  const redirectToPHP = () => {
    window.location.href = 'http://localhost/Sae_Semantix_S2/backend/main/menu.php';
  };

  useEffect(() => {
    const socketIo = io(SOCKET_SERVER_URL);
    setSocket(socketIo);

    
    const params = new URLSearchParams(window.location.search);
    const gamemode = params.get('mode');

    if (gamemode == 'solo'){
      setJoiner(false); setSolo(true); setOwner(true);
    }
    else if (gamemode == 'owner'){
      setJoiner(false); setSolo(false); setOwner(true);
    }
    else if (gamemode == 'joiner'){
      setJoiner(true); setSolo(false); setOwner(false);
    }

    socketIo.on('setGame', (id) => {
      setGameId(id);
    });

    socketIo.on('gameCreated', ({ id, words }, pseudo) => {
      setGameId(id);
      setStart(true);
      setNewPseudo(" " + pseudo);
      if (words && words.length === 2) {
        const [word1, word2] = words;
        const newNode1 = { id: nodes.length + 1, label: word1 };
        const newNode2 = { id: nodes.length + 2, label: word2 };
        nodes.add([newNode1, newNode2]);

        edges.add({ from: newNode1.id, to: newNode2.id });
      }
    });

    socketIo.on('joinedGame', (id: string, pseudo, node, edge) => {
      nodes.clear();
      edges.clear();
      nodes.add(node);
      edges.add(edge);
      console.log('Celui qui a rejoint est : ' + pseudo);
      setGameId(id);
      setNewPseudo(" " + pseudo);
      //setListPlayer((prevPlayers) => [...prevPlayers, pseudo]);
      setStart(true);
    });

    socketIo.on('update network', ({ nodes: newNodes, edges: newEdges }) => {
      nodes.clear();
      nodes.add(newNodes);
      edges.clear();
      edges.add(newEdges);
    });

    socketIo.on('update score', (newScore) => {
      console.log('Score avant changement ' + newScore);
      setScore(newScore);
    });

    socketIo.on('not your turn', () => {
      alert('Ce n\'est pas votre tour!');
    });

    socketIo.on('your turn', () => {
      setIsMyTurn(true);
    });

    socketIo.on('chat message', (msg) => {
      setMessages((prevMessages) => [...prevMessages, msg]);
    });

    socketIo.on('startTimer', ({ duration, startTime }) => {
      const remainingTime = duration - (Date.now() - startTime);
      setTimerProps((prevProps) => ({
        ...prevProps,
        seconds: remainingTime / 1000,
        onTimerEnd: handleTimerEnd,
      }));
      timerRef.current?.startTimer();
    });

    socketIo.on('timerUpdate', ({ remainingTime }) => {
      setTimerProps((prevProps) => ({
        ...prevProps,
        seconds: remainingTime / 1000,
        onTimerEnd: handleTimerEnd,
      }));
      timerRef.current?.startTimer();
    });

    socketIo.on('timerEnd', async (id: string, players) => {
      console.log("jeongiugnihoiyfgbufybuçfyhbfyuhugy : " + id);
      if (!id) return;
        const date = new Date().toISOString().split('T')[0]; // Get current date in YYYY-MM-DD format
        const playersList = pseudo; // Assuming players are stored in the pseudo state

        const params = new URLSearchParams(window.location.search);
        const gamemode = params.get('mode');

        try {
          await axios.get(`http://localhost/Sae_Semantix_S2/save_game.php?id=${id}&players=${players}&nodeCount=${nodes.length}&score=${score}&date=${date}`);
          console.log('Game results saved successfully');
        } catch (error) {
          console.error('Error saving game results:', error);
        }
      handleTimerEnd();
      console.log("dklf,l,oizoijzodjfo");
    });

    socketIo.on('gameFinish', () => {
      setFinish(true);
    });

    socketIo.on('error', (message) => {
      alert(message);
    });

    return () => {
      socketIo.disconnect();
    };
  }, [nodes, edges]);

  useEffect(() => {
    if (networkContainer.current) {
      const data = {
        nodes: nodes,
        edges: edges,
      };

      const options = {
        nodes: {
          shape: 'dot',
          size: 30,
          font: {
            size: 14,
            color: 'black',
          },
          borderWidth: 3,
          color: {
            border: '#91755b', // Couleur de la bordure des cercles
            background: ' #138d75  ', // Couleur de fond des cercles
            highlight: {
              border: '#91755b', // Couleur de la bordure des cercles surlignés
              background: ' #138d75  ', // Couleur de fond des cercles surlignés
            },
            hover: {
              border: '#2B7CE9', // Couleur de la bordure des cercles survolés
              background: '#D2E5FF', // Couleur de fond des cercles survolés
            },
          },
        },
        edges: {
          width: 3,
          color: '91755b',
        },
      };

      const networkInstance = new Network(networkContainer.current, data, options);
      setNetwork(networkInstance);
    }
  }, [nodes, edges]);

  const createGame = () => {
    const params = new URLSearchParams(window.location.search);
    const usernameFromURL = params.get('username');
    setOwner(true);
    if (socket) {
      socket.emit('createGame', usernameFromURL);
    }
  };

  const joinGame = (id: string) => {
    setInput(true);
    const params = new URLSearchParams(window.location.search);
    const usernameFromURL = params.get('username');
    if (socket) {
      socket.emit('joinGame', id, usernameFromURL);
    }
  };

  const handleAddWord = () => {
    if (socket && word.trim() !== '') {
      socket.emit('new word', word);
      setWord('');
      setIsMyTurn(false);
    }
  };

  const handleSendMessage = () => {
    if (socket && newMessage.trim() !== '') {
      const msg = { id: socket.id, message: newMessage };
      socket.emit('chat message', msg);
      setNewMessage('');
    }
  };

  const startGame = (id: string | null) => {
    setInput(true);
    if (socket) {
      socket.emit('chrono', id);
    }
  };

  return (
    <div>
      <div style={{ display: isFinish ? 'none' : 'block' }}>
        <div style={styles.topLeftCorner}>
          <button onClick={redirectToPHP}>Revenir à l'accueil</button>
        </div>
        <div ref={networkContainer} style={{ height: '500px' }} />
        <div>
          <p style={{display: isSolo || isJoiner ? 'none' : ''}}>ID de jeu: {gameId}</p>
          <div style={{ display: isStart ? 'none' : 'block' }}>
            <button
              onClick={() => {
                createGame();
              }}
              style={{display: isJoiner ? 'none' : ''}}
            >
              Lancer la partie
            </button>
            <input
              type="text"
              placeholder="Entrez l'id du jeu"
              onBlur={(e) => joinGame(e.target.value)}
              style={{display: isOwner || isSolo ? 'none' : ''}}
            />
          </div>
          <div style={{ display: isStart && !isEnabled && isOwner ? 'block' : 'none' }}>
              <button onClick={() => {startGame(gameId); 
                timerRef.current?.startTimer();}}>Lancer</button>
          </div>
        </div>
        <div style={{ display: isStart ? 'block' : 'none' }}>
          <div>
            <input
              type="text"
              value={word}
              placeholder='Ajouter un mot'
              onChange={(e) => setWord(e.target.value)}
              onKeyPress={(e) => (e.key === 'Enter' ? handleAddWord() : null)}
              disabled={!isMyTurn}
            />
            <button onClick={handleAddWord} disabled={!isMyTurn || !isEnabled}>
              Ajouter
            </button>
          </div>
          <div>
            <h2>Score: {score}</h2>
          </div>
          <div className="BoxContainer">
            <div className="ChatContainer">
              <h3>Chat</h3>
              <div
                style={{
                  height: '150px',
                  overflowY: 'scroll',
                  border: '1px solid black',
                  padding: '5px',
                }}
              >
                {messages.map((msg, index) => (
                  <div key={index}>
                    <strong>{msg.id}</strong>: {msg.message}
                  </div>
                ))}
              </div>
              <input
                type="text"
                value={newMessage}
                onChange={(e: React.ChangeEvent<HTMLInputElement>) => setNewMessage(e.target.value)}
                onKeyPress={(e) => (e.key === 'Enter' ? handleSendMessage() : null)}
              />
              <button onClick={handleSendMessage}>Envoyer</button>
            </div>
            <div className="PlayerContainer">
              <h3>Joueurs :</h3>
              <ul>
                <PseudoList />
              </ul>
            </div>
          </div>
        </div>
        <div style={styles.topRightCorner}>
          <Timer {...timerProps} ref={timerRef} />
        </div>
      </div>
      <div style={{ display: isFinish ? 'block' : 'none' }}>
        <div style={{ backgroundColor: 'ceb59e' }}>
          <h1>Partie terminée !</h1>
          <h2>Score final: {score}</h2>
          <h2>Bravo à vous !</h2>
          <button onClick={refreshPage}>Nouvelle partie</button>
          <button onClick={redirectToPHP}>Revenir à l'accueil</button>
        </div>
      </div>
    </div>
  );
};

const styles = {
  topRightCorner: {
    position: 'absolute',
    top: '10px',
    right: '10px',
  } as React.CSSProperties,
  topLeftCorner: {
    position: 'absolute',
    bottom: '10px',
    left: '10px',
  } as React.CSSProperties,
};

export default NetworkGraph;
