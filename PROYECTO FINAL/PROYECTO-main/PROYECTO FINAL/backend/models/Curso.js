const mongoose = require('mongoose');

const CursoSchema = new mongoose.Schema({
  titulo: String,
  descripcion: String,
  fechaInicio: Date,
  duracion: Number,
  precio: Number,
  imagen: String,
  creador: { type: mongoose.Schema.Types.ObjectId, ref: 'Usuario' }
});

module.exports = mongoose.model('Curso', CursoSchema);
