const mongoose = require('mongoose');

const UsuarioSchema = new mongoose.Schema({
  nombre: String,
  email: { type: String, unique: true },
  contraseña: String,
  cursosSuscritos: [{ type: mongoose.Schema.Types.ObjectId, ref: 'Curso' }],
  cursosPublicados: [{ type: mongoose.Schema.Types.ObjectId, ref: 'Curso' }]
});

module.exports = mongoose.model('Usuario', UsuarioSchema);
