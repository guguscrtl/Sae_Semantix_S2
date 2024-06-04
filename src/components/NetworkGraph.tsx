import React, { useEffect, useRef, useState } from 'react';
import { DataSet, Network } from 'vis-network/standalone/esm/vis-network';
import { io, Socket } from 'socket.io-client';
import 'vis-network/styles/vis-network.css';
import Timer, { Props } from './Timer';

const SOCKET_SERVER_URL = 'http://localhost:3001';

const NetworkGraph: React.FC = () => {
  const networkContainer = useRef<HTMLDivElement | null>(null);
  const timerRef = useRef<Timer>(null); // useRef for Timer
  const [network, setNetwork] = useState<Network | null>(null);
  const [nodes, setNodes] = useState<DataSet<any>>(new DataSet([]));
  const [edges, setEdges] = useState<DataSet<any>>(new DataSet([]));
  const [socket, setSocket] = useState<Socket | null>(null);
  const [word, setWord] = useState<string>('');
  const [isMyTurn, setIsMyTurn] = useState<boolean>(false);
  const [score, setScore] = useState<number>(0);
  const [messages, setMessages] = useState<Array<{ id: string, message: string }>>([]);
  const [players, setPlayers] = useState<Array<{pseudo: string}>>([]);
  const [newMessage, setNewMessage] = useState<string>('');
  const [gameId, setGameId] = useState<string | null>(null);
  const [liste_player, setListPlayer] = useState<string[]>([]);
  const [isStart, setStart] = useState<boolean>(false);
  const [isFinish, setFinish] = useState<boolean>(false);
  const [timerProps, setTimerProps] = useState<Props>({
    seconds: 30,
    size: 70,
    strokeBgColor: "lightgray",
    strokeColor: "lightgreen",
    strokeWidth: 10,
    onTimerEnd: () => handleTimerEnd(),
  });

  const handleTimerEnd = () => {
    setTimerProps({
      seconds: 120,
      size: 70,
      strokeBgColor: "lightgray",
      strokeColor: "lightblue",
      strokeWidth: 10,
      onTimerEnd: () => setFinish(true),
    });
    timerRef.current?.startTimer();
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
  
    socketIo.on('setGame', (id) => {
      setGameId(id);
    });

    socketIo.on('gameCreated', ({ id, words }, pseudo) => {
      console.log("mon pseudo est :" + pseudo);  
      setGameId(id);
  
      if (words && words.length === 2) {
        const [word1, word2] = words;
        const newNode1 = { id: nodes.length + 1, label: word1 };
        const newNode2 = { id: nodes.length + 2, label: word2 };
        nodes.add([newNode1, newNode2]);
  
        edges.add({ from: newNode1.id, to: newNode2.id });
        setListPlayer(prevPlayers => [ ...prevPlayers, pseudo]);
      }
    });

    socketIo.on('joinedGame', (id: string, pseudo, words) => {
      nodes.clear();
      edges.clear();
      if (words && words.length === 2) {
        const [word1, word2] = words;
        console.log("mot1: " + word1);
        console.log("mot2: " + word2);
        const newNode1 = { id: nodes.length + 1, label: word1 };
        const newNode2 = { id: nodes.length + 2, label: word2 };
        nodes.add([newNode1, newNode2]);
  
        edges.add({ from: newNode1.id, to: newNode2.id });
      }
      console.log('Celui qui a rejoint est : ' + pseudo)
      setGameId(id);
      setListPlayer(prevPlayers => [ ...prevPlayers, pseudo]);
      setStart(true);
    });

    socketIo.on('update network', ({ nodes: newNodes, edges: newEdges }) => {
      nodes.clear();
      nodes.add(newNodes);
      edges.clear();
      edges.add(newEdges);
    });

    socketIo.on('update score', (newScore) => {
      console.log("Score avant changement " + newScore);
      setScore(newScore);
    });

    socketIo.on('not your turn', () => {
      alert('Ce n\'est pas votre tour!');
    });

    socketIo.on('your turn', () => {
      setIsMyTurn(true);
    });

    socketIo.on('chat message', (msg) => {
      setMessages(prevMessages => [...prevMessages, msg]);
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
            color: '#000000',
          },
          borderWidth: 2,
        },
        edges: {
          width: 2,
          color: '#000000',
        },
      };

      const networkInstance = new Network(networkContainer.current, data, options);
      setNetwork(networkInstance);
    }
  }, [nodes, edges]);

  const createGame = () => {
    if (socket) {
      socket.emit('createGame');
    }
  };

  const joinGame = (id: string) => {
    if (socket) {
      socket.emit('joinGame', id);
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

  return (
    <div>
      <div style={{display: isFinish ? "none" : "block"}}>
        <div style={styles.topLeftCorner}><button onClick={redirectToPHP}>Revenir a l'acceuil</button></div>
        <div ref={networkContainer} style={{ height: '500px' }} />
        <div style={{display: isStart ? "none" : "block"}}> 
          <p>ID de jeu: {gameId}</p>
          <div>
            <button onClick={() => {
            createGame();
            timerRef.current?.startTimer();
          }}>Lancer la partie</button>
              <input
                type="text"
                placeholder="Entrez l'id du jeu"
                onBlur={(e) => joinGame(e.target.value)}
              />
          </div>
        </div>
        <div style={{display: isStart ? "block" : "none"}}>
          <div>
            <input
              type="text"
              value={word}
              onChange={(e) => setWord(e.target.value)}
              onKeyPress={(e) => e.key === 'Enter' ? handleAddWord() : null}
              disabled={!isMyTurn}
            />
            <button onClick={handleAddWord} disabled={!isMyTurn}>Ajouter le mot</button>
          </div>
          <div>
            <h2>Score: {score}</h2>
          </div>
          <div className='BoxContainer'>
            <div className="ChatContainer">
              <h3> Chat</h3>
              <div style={{ height: '150px', overflowY: 'scroll', border: '1px solid black', padding: '5px' }}>
                {messages.map((msg, index) => (
                  <div key={index}><strong>{msg.id}</strong>: {msg.message}</div>
                ))}
              </div>
              <input
                type="text"
                value={newMessage}
                onChange={(e: React.ChangeEvent<HTMLInputElement>) => setNewMessage(e.target.value)}
                onKeyPress={(e) => e.key === 'Enter' ? handleSendMessage() : null}
              />
              <button onClick={handleSendMessage}>Envoyer</button>
            </div>
            <div className="PlayerContainer">
              <h3>Joueurs :</h3>
              <ul>
                {liste_player.map((player, index) => (
                  <li key={index}>{player}</li>
                ))}
              </ul>
            </div>
          </div>
        </div>
        <div style={styles.topRightCorner}>
          <Timer {...timerProps} ref={timerRef} />
        </div>
        <div style={{display: isStart ? "none" : "block"}}>
          <h3>Joueurs :</h3>
          <ul>
            {liste_player.map((player, index) => (
              <li key={index}>{player}</li>
            ))}
          </ul>
        </div>
      </div>
      <div style={{display: isFinish ? "block" : "none"}}>
        <div style={{backgroundColor: "ceb59e"}}>
          <h1>Partie terminée !</h1>
          <h2>Score final: {score}</h2>
          <h2>Bravo à vous !</h2>
          <button onClick={refreshPage}>Nouvelle partie</button>
          <button onClick={redirectToPHP}>Revenir a l'acceuil</button>
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
