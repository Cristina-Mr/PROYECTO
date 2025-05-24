const express = require('express');
const router = express.Router();
const multer = require('multer');
const cursoController = require('../controllers/cursoController');

const storage = multer.diskStorage({
  destination: 'uploads/',
  filename: (req, file, cb) => cb(null, Date.now() + '-' + file.originalname)
});

const upload = multer({ storage });

router.post('/crear', upload.single('imagen'), cursoController.crearCurso);
router.get('/', cursoController.obtenerCursos);

module.exports = router;
