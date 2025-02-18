import React from 'react';
import { BrowserRouter, Routes, Route } from "react-router";
import Header from './components/partials/header'
import Home from './routes/home';
import Login from "./routes/login"; 


const App = () => {
  return (
    <BrowserRouter>
      <Header/>
      <Routes>
        <Route path='/' element={<Home/>} />
        <Route path='/login' element={<Login/>} />
      </Routes>
    </BrowserRouter>    
  );
};

export default App;