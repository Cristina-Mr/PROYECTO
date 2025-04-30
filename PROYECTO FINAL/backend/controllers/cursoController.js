const Curso = require('../models/Curso');
const Usuario = require('../models/Usuario');

exports.crearCurso = async (req, res) => {
  try {
    const { titulo, descripcion, fecha, duracion, precio, usuarioId } = req.body;
    const imagen = req.file.filename;

    const curso = new Curso({
      titulo,
      descripcion,
      fechaInicio: fecha,
      duracion,
      precio,
      imagen,
      creador: usuarioId
    });

    await curso.save();
    await Usuario.findByIdAndUpdate(usuarioId, { $push: { cursosPublicados: curso._id } });

    res.status(201).json({ mensaje: 'Curso publicado' });
  } catch (err) {
    res.status(500).json({ error: err.message });
  }
};

exports.obtenerCursos = async (req, res) => {
  const cursos = await Curso.find().populate('creador', 'nombre');
  res.json(cursos);
};
