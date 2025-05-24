const express = require('express');
const router = express.Router();
const usuarioController = require('../controllers/usuarioController');

router.post('/registro', usuarioController.registrar);
router.post('/login', usuarioController.login);
router.get('/perfil/:id', usuarioController.perfil);

module.exports = router;
