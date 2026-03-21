// ═══════════════════════════════════════════
// EduTube — Base de videos (hardcodeada)
// Se reemplazará por consultas a BD
// ═══════════════════════════════════════════

var CHANNELS = {
    'liberte': { nombre: 'Cooperativa Liberté', code: 'CL', color: '#2e8b47' },
    'infobae': { nombre: 'Infobae', code: 'IB', color: '#e63946' },
    'aterciopelados': { nombre: 'Aterciopelados', code: 'AT', color: '#9b5de5' },
    'a24': { nombre: 'A24', code: 'A24', color: '#f77f00' }
};

var VIDEOS = {
    // ── Cooperativa Liberté ──
    'Xw2zGp-Fjr8': {
        titulo: 'EL-280226 Adultocentrismo y producción de subjetividad en contextos de exclusión',
        descripcion: 'Encuentro Liberté del 28/02/2026. Análisis sobre adultocentrismo y la producción de subjetividad en contextos de exclusión social.',
        canal: 'liberte', categoria: 'Encuentros Liberté',
        duracion: '2:13:18', vistas: 101, fecha: '2026-03-01',
        tags: ['adultocentrismo', 'subjetividad', 'exclusión', 'derechos']
    },
    'iTGbnWacFxw': {
        titulo: '5ta Clase - 4to Cursos Mujeres Privadas de la Libertad en América Latina',
        descripcion: '5ta clase del 4to curso sobre Mujeres Privadas de la Libertad en América Latina.',
        canal: 'liberte', categoria: 'Cursos',
        duracion: '1:55:29', vistas: 163, fecha: '2026-02-21',
        tags: ['mujeres', 'libertad', 'latinoamérica', 'género', 'cárceles']
    },
    'bKnEOaW1hsc': {
        titulo: '4ta Clase - 4to Cursos Mujeres Privadas de la Libertad en América Latina',
        descripcion: '4ta clase del 4to curso sobre Mujeres Privadas de la Libertad en América Latina.',
        canal: 'liberte', categoria: 'Cursos',
        duracion: '1:53:42', vistas: 147, fecha: '2026-02-20',
        tags: ['mujeres', 'libertad', 'latinoamérica', 'género', 'cárceles']
    },
    'hHYjl0R0F3I': {
        titulo: '3er Clase - 4to Cursos Mujeres Privadas de la Libertad en América Latina',
        descripcion: '3er clase del 4to curso sobre Mujeres Privadas de la Libertad en América Latina.',
        canal: 'liberte', categoria: 'Cursos',
        duracion: '1:54:10', vistas: 180, fecha: '2026-02-19',
        tags: ['mujeres', 'libertad', 'latinoamérica', 'género', 'cárceles']
    },
    'T4XPU8vvviw': {
        titulo: '2da Clase - 4to Cursos Mujeres Privadas de la Libertad en América Latina',
        descripcion: '2da clase del 4to curso sobre Mujeres Privadas de la Libertad en América Latina.',
        canal: 'liberte', categoria: 'Cursos',
        duracion: '1:49:17', vistas: 206, fecha: '2026-02-18',
        tags: ['mujeres', 'libertad', 'latinoamérica', 'género', 'cárceles']
    },
    '9-6TrW8JBWg': {
        titulo: '1er Clase - 4to Cursos Mujeres Privadas de la Libertad en América Latina',
        descripcion: '1er clase del 4to curso sobre Mujeres Privadas de la Libertad en América Latina.',
        canal: 'liberte', categoria: 'Cursos',
        duracion: '1:48:40', vistas: 339, fecha: '2026-02-17',
        tags: ['mujeres', 'libertad', 'latinoamérica', 'género', 'cárceles']
    },
    'pqs4xltUE48': {
        titulo: 'EL-310126',
        descripcion: 'Encuentro Liberté del 31 de enero de 2026.',
        canal: 'liberte', categoria: 'Encuentros Liberté',
        duracion: '2:11:00', vistas: 124, fecha: '2026-02-01',
        tags: ['encuentro', 'liberté', 'debate']
    },
    'BRHahRFVmQ8': {
        titulo: 'Clase 15 - Cambio de Paradigma - Sábado 13/12/2025',
        descripcion: 'Clase 15 del curso Cambio de Paradigma.',
        canal: 'liberte', categoria: 'Cambio de Paradigma',
        duracion: '3:00:36', vistas: 236, fecha: '2025-12-14',
        tags: ['paradigma', 'cambio', 'sociedad']
    },
    'ZbSTrUSRU-4': {
        titulo: 'Clase 14 - Salud Mental - Sábado 29/11/2025',
        descripcion: 'Clase 14 sobre Salud Mental.',
        canal: 'liberte', categoria: 'Cambio de Paradigma',
        duracion: '3:00:01', vistas: 396, fecha: '2025-11-30',
        tags: ['salud mental', 'bienestar', 'paradigma']
    },
    'hO0V0y_Yn2U': {
        titulo: 'Clase 9 - Última Jornada',
        descripcion: 'Clase 9, última jornada del curso.',
        canal: 'liberte', categoria: 'Cambio de Paradigma',
        duracion: '1:29:26', vistas: 107, fecha: '2025-11-29',
        tags: ['jornada', 'paradigma', 'cierre']
    },

    // ── Infobae ──
    'FgAshcmmXwE': {
        titulo: 'Diego Golombek y el sueño de los argentinos',
        descripcion: 'Entrevista con el científico Diego Golombek sobre el sueño y los hábitos de los argentinos.',
        canal: 'infobae', categoria: 'Entrevistas',
        duracion: '4:20', vistas: 0, fecha: '2026-03-21',
        tags: ['golombek', 'sueño', 'ciencia', 'argentina']
    },
    'CBIa91ZbRKs': {
        titulo: 'Los protagonistas del Juicio a las Juntas: Gil Lavedra y Arslanian en una entrevista clave',
        descripcion: 'Entrevista con los jueces Gil Lavedra y Arslanian sobre el histórico Juicio a las Juntas.',
        canal: 'infobae', categoria: 'Entrevistas',
        duracion: '1:07:25', vistas: 727, fecha: '2026-03-21',
        tags: ['juicio', 'juntas', 'justicia', 'derechos humanos', 'argentina']
    },
    'UJ_g4nUiq6E': {
        titulo: 'CRECIÓ LA ECONOMÍA, CARLOS KIKUCHI, SOBRE LA FELICIDAD, GIL LAVEDRA Y ARSLANIAN (20/3)',
        descripcion: 'Programa completo del 20 de marzo de 2026.',
        canal: 'infobae', categoria: 'Programas',
        duracion: '3:04:04', vistas: 10364, fecha: '2026-03-21',
        tags: ['economía', 'kikuchi', 'felicidad', 'actualidad']
    },
    'JuZ8mUowW90': {
        titulo: 'Estafa piramidal en Mendoza: prometían dólares y hay 11 denuncias',
        descripcion: 'Investigación sobre una estafa piramidal en Mendoza con múltiples denuncias.',
        canal: 'infobae', categoria: 'Noticias',
        duracion: '2:48', vistas: 14917, fecha: '2026-03-21',
        tags: ['estafa', 'mendoza', 'dólares', 'denuncia']
    },
    'gexeKgsInjM': {
        titulo: 'Un abrazo puede bajar el estrés: lo que dicen los estudios sobre redes y salud mental',
        descripcion: 'Análisis de estudios sobre el impacto de las redes sociales en la salud mental.',
        canal: 'infobae', categoria: 'Noticias',
        duracion: '2:36', vistas: 3586, fecha: '2026-03-20',
        tags: ['salud mental', 'redes sociales', 'estrés', 'estudios']
    },
    'kmL-mGg80gE': {
        titulo: 'Fue a denunciar una estafa y terminó golpeada en una comisaría',
        descripcion: 'Caso de violencia policial contra una mujer que fue a realizar una denuncia.',
        canal: 'infobae', categoria: 'Noticias',
        duracion: '1:57', vistas: 6321, fecha: '2026-03-20',
        tags: ['violencia', 'policial', 'denuncia', 'comisaría']
    },
    'lJKoNm-c1Ow': {
        titulo: 'Carlos Kikuchi en Infobae: críticas a la economía real y diálogo con distintos espacios',
        descripcion: 'Entrevista con Carlos Kikuchi sobre la situación económica actual.',
        canal: 'infobae', categoria: 'Entrevistas',
        duracion: '22:15', vistas: 2141, fecha: '2026-03-20',
        tags: ['kikuchi', 'economía', 'política', 'diálogo']
    },
    'cCV73Z_0BcY': {
        titulo: 'A 6 AÑOS DE LA CUARENTENA, JUAMPI GONZÁLEZ, ALEXANDRA KOHAN (20/3)',
        descripcion: 'Programa completo del 20 de marzo. Reflexiones a 6 años de la cuarentena.',
        canal: 'infobae', categoria: 'Programas',
        duracion: '3:05:15', vistas: 14235, fecha: '2026-03-20',
        tags: ['cuarentena', 'pandemia', 'reflexión', 'kohan']
    },
    'sOMaZdqk81g': {
        titulo: 'Alexandra Kohan en Infobae: reflexiones sobre vínculos, productividad y cultura actual',
        descripcion: 'Entrevista con Alexandra Kohan sobre vínculos y cultura contemporánea.',
        canal: 'infobae', categoria: 'Entrevistas',
        duracion: '39:50', vistas: 819, fecha: '2026-03-20',
        tags: ['kohan', 'vínculos', 'cultura', 'productividad']
    },
    'K6JUbBZkxR8': {
        titulo: 'Más de 100 chicos desaparecidos en Argentina: historias sin respuesta',
        descripcion: 'Investigación sobre menores desaparecidos en Argentina.',
        canal: 'infobae', categoria: 'Noticias',
        duracion: '2:17', vistas: 12066, fecha: '2026-03-20',
        tags: ['desaparecidos', 'menores', 'argentina', 'investigación']
    },

    // ── Aterciopelados ──
    '9mzJSwUYvfA': {
        titulo: 'Alerta Bogotá!!!! 🚨',
        descripcion: 'Anuncio de Aterciopelados para Bogotá.',
        canal: 'aterciopelados', categoria: 'Música',
        duracion: '0:34', vistas: 1614, fecha: '2026-03-19',
        tags: ['bogotá', 'concierto', 'anuncio', 'rock']
    },
    'G1di6yOgTCA': {
        titulo: 'Encuentres el misterio, cultives un imperio, de música y amor 💜✨ #LaTetaPirata',
        descripcion: 'Adelanto del tema La Teta Pirata de Aterciopelados.',
        canal: 'aterciopelados', categoria: 'Música',
        duracion: '0:59', vistas: 881, fecha: '2026-03-08',
        tags: ['teta pirata', 'adelanto', 'rock latino']
    },
    'CzOD2autpEY': {
        titulo: 'Aterciopelados, Andrea Echeverri - La Teta Pirata (Video Oficial)',
        descripcion: 'Video oficial de La Teta Pirata, nuevo tema de Aterciopelados con Andrea Echeverri.',
        canal: 'aterciopelados', categoria: 'Música',
        duracion: '5:00', vistas: 23987, fecha: '2026-03-06',
        tags: ['teta pirata', 'video oficial', 'andrea echeverri', 'rock']
    },
    'SMr94ytUNjA': {
        titulo: '¿Cómo van esos propósitos de Año Nuevo? 🤣',
        descripcion: 'Aterciopelados reflexiona sobre los propósitos de año nuevo.',
        canal: 'aterciopelados', categoria: 'Música',
        duracion: '0:43', vistas: 1511, fecha: '2026-01-18',
        tags: ['año nuevo', 'humor', 'rock']
    },
    'qkdgVxii9xM': {
        titulo: 'Arrancando este 2026 ✨',
        descripcion: 'Aterciopelados arranca el 2026.',
        canal: 'aterciopelados', categoria: 'Música',
        duracion: '0:26', vistas: 1991, fecha: '2026-01-09',
        tags: ['2026', 'año nuevo', 'rock']
    },
    'VTKzXZ5ZIeo': {
        titulo: '"El Futuro es Ya" nuestra nueva canción junto a Wendy Sulca',
        descripcion: 'Anuncio del nuevo tema El Futuro es Ya junto a Wendy Sulca.',
        canal: 'aterciopelados', categoria: 'Música',
        duracion: '0:15', vistas: 3075, fecha: '2025-11-19',
        tags: ['futuro', 'wendy sulca', 'colaboración', 'rock']
    },
    'IqMTuZEcA7M': {
        titulo: '19 de noviembre de 2025',
        descripcion: 'Publicación del 19 de noviembre de 2025.',
        canal: 'aterciopelados', categoria: 'Música',
        duracion: '1:21', vistas: 471, fecha: '2025-11-19',
        tags: ['aterciopelados', 'noviembre']
    },
    '5QoiKHXpGME': {
        titulo: 'Aterciopelados, Wendy Sulca - El Futuro es Ya (Video Oficial)',
        descripcion: 'Video oficial de El Futuro es Ya, colaboración entre Aterciopelados y Wendy Sulca.',
        canal: 'aterciopelados', categoria: 'Música',
        duracion: '3:31', vistas: 678892, fecha: '2025-11-07',
        tags: ['futuro', 'wendy sulca', 'video oficial', 'rock latino']
    },
    'CG48ldVCngg': {
        titulo: '¡Vamos para CHÍA este 25 de octubre! 🇨🇴',
        descripcion: 'Anuncio de concierto en Chía, Colombia.',
        canal: 'aterciopelados', categoria: 'Música',
        duracion: '0:31', vistas: 2454, fecha: '2025-10-05',
        tags: ['chía', 'colombia', 'concierto']
    },
    '85dPSRJ158k': {
        titulo: 'Vamos para Madrid al festival Saca El Diablo 2025',
        descripcion: 'Anuncio de participación en el festival Saca El Diablo en Madrid.',
        canal: 'aterciopelados', categoria: 'Música',
        duracion: '0:17', vistas: 8090, fecha: '2025-09-08',
        tags: ['madrid', 'festival', 'saca el diablo', 'españa']
    },

    // ── A24 ──
    'QWjya-Xgizs': {
        titulo: 'EXCLUSIVO: LA FACTURA QUE COMPLICA A ADORNI',
        descripcion: 'Informe exclusivo sobre la factura que complica al vocero presidencial.',
        canal: 'a24', categoria: 'Noticias',
        duracion: '2:59', vistas: 4586, fecha: '2026-03-21',
        tags: ['adorni', 'factura', 'exclusivo', 'política']
    },
    'leo1wPQIhgo': {
        titulo: 'EXCLUSIVO: LOS AUDIOS DE LA CAUSA AMIA',
        descripcion: 'Revelación de audios exclusivos de la causa AMIA.',
        canal: 'a24', categoria: 'Noticias',
        duracion: '3:00', vistas: 13460, fecha: '2026-03-21',
        tags: ['amia', 'audios', 'justicia', 'exclusivo']
    },
    'GZaWwh_e9To': {
        titulo: 'EXCLUSIVO: LOS AUDIOS DE LA CAUSA AMIA',
        descripcion: 'Cobertura extendida de los audios de la causa AMIA.',
        canal: 'a24', categoria: 'Noticias',
        duracion: '18:19', vistas: 5059, fecha: '2026-03-21',
        tags: ['amia', 'audios', 'justicia', 'investigación']
    },
    'nwTyNk7_vro': {
        titulo: 'EXCLUSIVO: LA FACTURA QUE COMPLICA A ADORNI',
        descripcion: 'Informe extendido sobre la factura que complica a Adorni.',
        canal: 'a24', categoria: 'Noticias',
        duracion: '31:57', vistas: 24229, fecha: '2026-03-21',
        tags: ['adorni', 'factura', 'política', 'investigación']
    },
    'EC2uiBXS4zk': {
        titulo: 'NIÑA "E" NO SE EXTRAVIÓ, FUE CRIMEN ORGANIZADO',
        descripcion: 'Investigación sobre el caso de la niña E y su vínculo con el crimen organizado.',
        canal: 'a24', categoria: 'Noticias',
        duracion: '0:37', vistas: 20395, fecha: '2026-03-21',
        tags: ['crimen organizado', 'niña', 'investigación']
    },
    'AvNa1Tof4t4': {
        titulo: 'DÍA 21 DEL CONFLICTO EN EL MEDIO ORIENTE',
        descripcion: 'Cobertura del día 21 del conflicto en Medio Oriente.',
        canal: 'a24', categoria: 'Noticias',
        duracion: '2:46', vistas: 2894, fecha: '2026-03-21',
        tags: ['medio oriente', 'conflicto', 'internacional']
    },
    'MZVuZV7of_w': {
        titulo: 'RECHAZAN DENUNCIA DE VILLARRUEL A FEINMANN Y ROSSI',
        descripcion: 'La justicia rechaza la denuncia de Villarruel contra Feinmann y Rossi.',
        canal: 'a24', categoria: 'Noticias',
        duracion: '2:27', vistas: 14518, fecha: '2026-03-21',
        tags: ['villarruel', 'feinmann', 'rossi', 'justicia']
    },
    'd6htT3cbRu4': {
        titulo: 'PUTIN MANDA AL PSICÓLOGO A GENTE QUE NO QUIERE TENER HIJOS',
        descripcion: 'Putin propone enviar al psicólogo a quienes no quieran tener hijos.',
        canal: 'a24', categoria: 'Noticias',
        duracion: '1:24', vistas: 4539, fecha: '2026-03-21',
        tags: ['putin', 'rusia', 'natalidad', 'internacional']
    },
    'RwDdw7CEPGw': {
        titulo: 'NUEVOS ALLANAMIENTOS EN LA AFA',
        descripcion: 'Nuevos allanamientos en la Asociación del Fútbol Argentino.',
        canal: 'a24', categoria: 'Noticias',
        duracion: '2:33', vistas: 6871, fecha: '2026-03-21',
        tags: ['afa', 'fútbol', 'allanamientos', 'justicia']
    },
    'IM-xY1KUics': {
        titulo: 'LA IMAGEN DE JAVIER MILEI EN LA GENTE: EL PASE DE FEINMANN Y ROSSI',
        descripcion: 'Análisis de la imagen de Milei según encuestas. Pase de Feinmann y Rossi.',
        canal: 'a24', categoria: 'Noticias',
        duracion: '2:56', vistas: 10610, fecha: '2026-03-21',
        tags: ['milei', 'encuestas', 'feinmann', 'rossi', 'política']
    }
};
