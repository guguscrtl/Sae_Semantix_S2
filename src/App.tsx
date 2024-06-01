import React from 'react';
import './App.css';
import NetworkGraph from './components/NetworkGraph';
import Chat from './components/Chat';

const App: React.FC = () => {
  return (
    <div className="App">
      <NetworkGraph />
    </div>
  );
};

export default App;
