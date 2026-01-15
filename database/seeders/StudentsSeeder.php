<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentsSeeder extends Seeder
{
    public function run(): void
    {
        $students = [
            // 1-20
            ['name' => 'Gabriel Bastidas Firigua', 'identification' => '1075600285', 'birth_date' => '2010-07-18', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Laura Valentina Cangrejo Garcia', 'identification' => '1069305264', 'birth_date' => '2010-03-15', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Yurany Nayibe Celeita Ramirez', 'identification' => '1024560625', 'birth_date' => '2010-05-22', 'address' => 'Vereda Santana', 'is_active' => false],
            ['name' => 'Jeremy Alejandro Celeita Romero', 'identification' => '1014273594', 'birth_date' => '2010-06-18', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Erick Diaz Duran', 'identification' => '1075600413', 'birth_date' => '2010-07-05', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Alexis Diaz Gonzalez', 'identification' => '10755999965', 'birth_date' => '2010-08-12', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Juan Jose Diaz Sanchez', 'identification' => '1077730107', 'birth_date' => '2010-04-28', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Nikoll Sofia Firigua Pastor', 'identification' => '1081158792', 'birth_date' => '2010-09-14', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Leidy Jhoana Guerrero Leyton', 'identification' => '1075600398', 'birth_date' => '2010-10-20', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Valery Javela Diaz', 'identification' => '1075600412', 'birth_date' => '2010-11-06', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Ashly Mariana Matta Ortigoza', 'identification' => '1075600424', 'birth_date' => '2010-08-29', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Kevin Andres Mora Ortigoza', 'identification' => '1075600323', 'birth_date' => '2010-07-11', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Kevin Adrian Oliveros Peralta', 'identification' => '1075600369', 'birth_date' => '2010-05-03', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Jefferson Arley Riaño Ortigoza', 'identification' => '1110118036', 'birth_date' => '2010-09-25', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Nicolas Rojas Bocanegra', 'identification' => '1075289658', 'birth_date' => '2010-03-17', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Santiago Rojas Bocanegra', 'identification' => '1075289660', 'birth_date' => '2010-03-17', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Jeison Adrian Serrato Serrato', 'identification' => '1075600300', 'birth_date' => '2010-06-12', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Ingry Tatiana Torres Muñoz', 'identification' => '1075600308', 'birth_date' => '2010-10-08', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Paula Andrea Bastidas Bastidas', 'identification' => '1075600270', 'birth_date' => '2010-06-22', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Edna Yirley Arevalo Niño', 'identification' => '1075600253', 'birth_date' => '2010-05-15', 'address' => 'Vereda Santana', 'is_active' => false],
            
            // 21-40
            ['name' => 'Frank Bocanegra Correa', 'identification' => '1076912036', 'birth_date' => '2009-04-15', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Karolay Shtefany Cortes Garcia', 'identification' => '1105614801', 'birth_date' => '2009-06-22', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Danna Valentina Firigua Pastor', 'identification' => '1077866842', 'birth_date' => '2009-08-10', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Isabela Gonzalez Garcia', 'identification' => '1075600062', 'birth_date' => '2009-05-17', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Danna Yeraldin Loaiza Torres', 'identification' => '1072105784', 'birth_date' => '2009-09-23', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Elizabeth Lozano Cardoso', 'identification' => '1075600084', 'birth_date' => '2009-07-05', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Diana Sofia Torres Garcia', 'identification' => '1075600156', 'birth_date' => '2009-03-14', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Samuel David Valencia Martinez', 'identification' => '1065807932', 'birth_date' => '2009-10-30', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Andrei Alexander Bolaños Lozano', 'identification' => '1110117785', 'birth_date' => '2008-05-12', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Dylan Steward Chamorro Bastidas', 'identification' => '1077728949', 'birth_date' => '2008-07-22', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Karen Lizeth Diaz Montero', 'identification' => '1077234690', 'birth_date' => '2008-03-18', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Carlos Davinson Garcia Diaz', 'identification' => '1110528558', 'birth_date' => '2008-09-10', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Alejandra Garcia Niño', 'identification' => '1075600055', 'birth_date' => '2008-06-14', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Lenny Margarita Guerrero Benavides', 'identification' => '1075599733', 'birth_date' => '2008-04-25', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Danna Lorena Guerrero Guerrero', 'identification' => '1075600081', 'birth_date' => '2008-08-09', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Yalena Lozano Cardoso', 'identification' => '1075599831', 'birth_date' => '2008-10-17', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Orlando Matta Gamboa', 'identification' => '1075600114', 'birth_date' => '2008-02-28', 'address' => 'Vereda Santana', 'is_active' => false],
            ['name' => 'Heidy Yuliany Parga Molina', 'identification' => '1075600014', 'birth_date' => '2008-11-05', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Diana Lisseth Coronado Hernandez', 'identification' => '1075599734', 'birth_date' => '2007-05-18', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Karol Yulieth Garcia Castro', 'identification' => '1075599562', 'birth_date' => '2007-07-29', 'address' => 'Vereda Santana', 'is_active' => false],
            
            // 41-60
            ['name' => 'Laura Valentina Garcia Garcia', 'identification' => '1075599774', 'birth_date' => '2007-04-12', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Kevin Albeiro Garcia Martinez', 'identification' => '1058671581', 'birth_date' => '2007-09-05', 'address' => 'Vereda Santana', 'is_active' => false],
            ['name' => 'Diego Stiven Garcia Ortigoza', 'identification' => '1075599460', 'birth_date' => '2007-06-17', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Nicolas Leal Ortigoza', 'identification' => '1075599692', 'birth_date' => '2007-03-22', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Luz Angela Ortigoza Garcia', 'identification' => '1075599729', 'birth_date' => '2007-10-08', 'address' => 'Vereda Santana', 'is_active' => false],
            ['name' => 'Karol Yuliana Rojas Cordoba', 'identification' => '1140922486', 'birth_date' => '2007-11-14', 'address' => 'Vereda Santana', 'is_active' => false],
            ['name' => 'Valentina Serrato Rodriguez', 'identification' => '1075599738', 'birth_date' => '2007-08-27', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Jainover Tique Bastidas', 'identification' => '1076600118', 'birth_date' => '2007-02-15', 'address' => 'Vereda Santana', 'is_active' => false],
            ['name' => 'Karol Tatiana Alape Garcia', 'identification' => '1075599611', 'birth_date' => '2006-04-12', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Andres Santiago Cangrejo Tique', 'identification' => '1075599441', 'birth_date' => '2006-05-18', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Jhon Jairo Cardoso Ramirez', 'identification' => '1075798223', 'birth_date' => '2006-07-24', 'address' => 'Vereda Santana', 'is_active' => false],
            ['name' => 'Jhojan Fermin Cardozo Duran', 'identification' => '1123808030', 'birth_date' => '2006-08-10', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Danni Arley Celeita Parra', 'identification' => '1069230634', 'birth_date' => '2006-06-27', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Ana Maria Diaz Lozano', 'identification' => '1077232908', 'birth_date' => '2006-03-15', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Steveen Alejandro Duran Amador', 'identification' => '1023388062', 'birth_date' => '2006-02-08', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Diego Alejandro Firigua Lopez', 'identification' => '1110117524', 'birth_date' => '2006-09-20', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Yeison Elieser Garcia Benavides', 'identification' => '1028784219', 'birth_date' => '2006-11-05', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Jhon Fener Guerrero Leyton', 'identification' => '1083839535', 'birth_date' => '2006-01-19', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Yuliana Guerrero Ortigoza', 'identification' => '1075599695', 'birth_date' => '2006-12-08', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Camilo Andres Matta Cardozo', 'identification' => '1075599598', 'birth_date' => '2006-04-30', 'address' => 'Vereda Santana', 'is_active' => true],
            
            // 61-80
            ['name' => 'Santiago Matta Parga', 'identification' => '1076908656', 'birth_date' => '2006-05-23', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Jeison Andres Mayorga Garcia', 'identification' => '1110117218', 'birth_date' => '2006-07-14', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Maria Jose Peña Buitrago', 'identification' => '1076908856', 'birth_date' => '2006-08-30', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Maria Lucia Sanchez Lozano', 'identification' => '1077727213', 'birth_date' => '2006-09-11', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Carlos Tique Bastidas', 'identification' => '1075600117', 'birth_date' => '2006-10-25', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Santiago Alape Garcia', 'identification' => '1022964491', 'birth_date' => '2005-03-18', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Briyith Mariana Cangrejo Guerrero', 'identification' => '1075599425', 'birth_date' => '2005-04-25', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Estefania Cardoso Rodriguez', 'identification' => '1075599170', 'birth_date' => '2005-06-12', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Valentina Castañeda Diaz', 'identification' => '1075599285', 'birth_date' => '2005-07-08', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Juan Felipe Concha Gonzalez', 'identification' => '1075796416', 'birth_date' => '2005-02-14', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Alison Duran Cardozo', 'identification' => '1023377237', 'birth_date' => '2005-09-20', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Paola Andrea Garcia Molina', 'identification' => '1074576636', 'birth_date' => '2005-05-15', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Andrea Garcia Quevedo', 'identification' => '1075599004', 'birth_date' => '2005-11-27', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Maria Jose Gonzalez Lozano', 'identification' => '1075599084', 'birth_date' => '2007-12-03', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Daniela Guerrero Juspian', 'identification' => '1058670894', 'birth_date' => '2005-08-18', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Estefania Guerrero Niño', 'identification' => '1075599402', 'birth_date' => '2005-01-23', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Paula Andrea Hernandez Cangrejo', 'identification' => '1075599506', 'birth_date' => '2005-10-05', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Santiago Hernandez Trujillo', 'identification' => '1075599245', 'birth_date' => '2005-03-28', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Francy Elena Mayorga Bastidas', 'identification' => '1075236370', 'birth_date' => '2005-06-30', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Diego Alejandro Peña Hernandez', 'identification' => '1077726506', 'birth_date' => '2005-12-14', 'address' => 'Vereda Santana', 'is_active' => true],
            
            // 81-100
            ['name' => 'Rafael Alberto Ramirez Gaitan', 'identification' => '1075238141', 'birth_date' => '2005-02-08', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Jhoiner Serrato Rodriguez', 'identification' => '1075247085', 'birth_date' => '2005-07-19', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Melany Sofia Diaz Peña', 'identification' => '1075601082', 'birth_date' => '2015-04-18', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Wendy Johanna Lozano Pastor', 'identification' => '1075601043', 'birth_date' => '2015-06-22', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Sharit Sofia Mendez Rodriguez', 'identification' => '1073723358', 'birth_date' => '2015-05-15', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Eliss Zamuel Solorzano Otaya', 'identification' => '1077881215', 'birth_date' => '2015-08-10', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Thiago David Torres Mora', 'identification' => '1011252543', 'birth_date' => '2015-07-05', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Breyner Andres Cangrejo Firigua', 'identification' => '1075600999', 'birth_date' => '2014-06-18', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Pedro Luis Firigua Lopez', 'identification' => '1077247055', 'birth_date' => '2014-09-22', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Luis Miguel Garcia Bastidas', 'identification' => '1075600743', 'birth_date' => '2014-05-10', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Maicol Andres Hernandez Mora', 'identification' => '1109006056', 'birth_date' => '2014-08-25', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Dayana Michell Muñoz Pabon', 'identification' => '1075600885', 'birth_date' => '2014-07-14', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Yudi Vanessa Perez Lizcano', 'identification' => '1117837306', 'birth_date' => '2014-04-05', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Breyner Kaleth Ramirez Cardozo', 'identification' => '1075807049', 'birth_date' => '2014-10-18', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Maria Jose Rodriguez Garcia', 'identification' => '1070019627', 'birth_date' => '2014-11-30', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Hasly Michel Cortez Garcia', 'identification' => '1021317448', 'birth_date' => '2013-05-14', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Heidy Liceth Pastor Guerrero', 'identification' => '1075600763', 'birth_date' => '2013-08-22', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Betulia Tique Bastidas', 'identification' => '1075600876', 'birth_date' => '2013-10-05', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Jhojan Darley Hernandez Garcia', 'identification' => '1075600495', 'birth_date' => '2012-03-18', 'address' => 'Vereda Santana', 'is_active' => true],
            ['name' => 'Yesid Quintero Garzon', 'identification' => '1109245140', 'birth_date' => '2012-06-25', 'address' => 'Vereda Santana', 'is_active' => true],
            
            // 101-142 (Bloque 3 - Final)
            ['name' => 'Brayan Alexander Garcia Juspian', 'identification' => '1075601130', 'birth_date' => '2018-06-18', 'address' => 'Vereda San Isidro', 'is_active' => true],
            ['name' => 'Faisury Garcia Juspian', 'identification' => '1075600961', 'birth_date' => '2016-07-25', 'address' => 'Vereda San Isidro', 'is_active' => true],
            ['name' => 'Lina Maria Bocanegra Correa', 'identification' => '1028670326', 'birth_date' => '2014-05-20', 'address' => 'Vereda San Isidro', 'is_active' => true],
            ['name' => 'Emerson David Osorio Peralta', 'identification' => '1016735170', 'birth_date' => '2013-07-15', 'address' => 'Vereda San Isidro', 'is_active' => true],
            ['name' => 'Heidy Valentina Murcia Argote', 'identification' => '1075601131', 'birth_date' => '2020-05-04', 'address' => 'La Cabaña', 'is_active' => true],
            ['name' => 'Jhosman Andres Murcia Argote', 'identification' => '1075805832', 'birth_date' => '2016-06-06', 'address' => 'La Cabaña', 'is_active' => true],
            ['name' => 'Naomi Alejandra Cangrejo Arellano', 'identification' => '1110118927', 'birth_date' => '2020-03-20', 'address' => 'Santa Ana', 'is_active' => true],
            ['name' => 'Laura Camila Guerrero Pérez', 'identification' => '1081183634', 'birth_date' => '2020-05-04', 'address' => 'La Esperanza', 'is_active' => true],
            ['name' => 'Darwin Guerrero Perez', 'identification' => '1081184201', 'birth_date' => '2019-04-03', 'address' => 'La Esperanza', 'is_active' => true],
            ['name' => 'Maille Celeste Bahos Hernández', 'identification' => '1075324190', 'birth_date' => '2019-02-14', 'address' => 'VDA. San Marcos', 'is_active' => true],
            ['name' => 'Maria José Gutiérrez Torres', 'identification' => '1075605065', 'birth_date' => '2018-12-22', 'address' => 'VDA. San Marcos', 'is_active' => true],
            ['name' => 'Mara Victoria Matta Bastidas', 'identification' => '1075324340', 'birth_date' => '2019-03-30', 'address' => 'VDA. San Marcos', 'is_active' => true],
            ['name' => 'Alejandro Guerrero Matta', 'identification' => '1075600964', 'birth_date' => '2017-09-27', 'address' => 'VDA. San Marcos', 'is_active' => true],
            ['name' => 'Wilmar Lozano Cardozo', 'identification' => '1110118607', 'birth_date' => '2017-01-02', 'address' => 'VDA. San Marcos', 'is_active' => true],
            ['name' => 'Sebastián Lugo Cardozo', 'identification' => '1075601009', 'birth_date' => '2018-02-19', 'address' => 'VDA. San Marcos', 'is_active' => true],
            ['name' => 'Francisco Cardozo Rodríguez', 'identification' => '1075600748', 'birth_date' => '2016-05-22', 'address' => 'VDA. San Marcos', 'is_active' => true],
            ['name' => 'Ana Sofia Gaitán Suarez', 'identification' => '1075600832', 'birth_date' => '2016-11-14', 'address' => 'VDA. San Marcos', 'is_active' => true],
            ['name' => 'Alejandro Matta Marroquín', 'identification' => '1075600780', 'birth_date' => '2016-08-09', 'address' => 'VDA. San Marcos', 'is_active' => true],
            ['name' => 'Maria del Mar Naranjo García', 'identification' => '1077245777', 'birth_date' => '2017-01-01', 'address' => 'VDA. San Marcos', 'is_active' => true],
            ['name' => 'Emmanuel Rodríguez Hernández', 'identification' => '1075600739', 'birth_date' => '2016-04-28', 'address' => 'VDA. San Marcos', 'is_active' => true],
            ['name' => 'Moisés Barrera Medina', 'identification' => '1070620834', 'birth_date' => '2015-02-24', 'address' => 'VDA. San Marcos', 'is_active' => true],
            ['name' => 'Johan Smeth González Lozano', 'identification' => '1110118335', 'birth_date' => '2015-02-12', 'address' => 'VDA. San Marcos', 'is_active' => true],
            ['name' => 'María Lucia Marroquín Rodríguez', 'identification' => '1075304654', 'birth_date' => '2015-06-05', 'address' => 'VDA. San Marcos', 'is_active' => true],
            ['name' => 'Sofia Matta García', 'identification' => '1075600716', 'birth_date' => '2016-02-21', 'address' => 'VDA. San Marcos', 'is_active' => true],
            ['name' => 'Milán Andrés Matta Ortigoza', 'identification' => '1073711419', 'birth_date' => '2015-09-20', 'address' => 'VDA. San Marcos', 'is_active' => true],
            ['name' => 'Salome Trujillo Ávila', 'identification' => '1079185856', 'birth_date' => '2016-04-21', 'address' => 'VDA. San Marcos', 'is_active' => true],
            ['name' => 'Danna Sofia Serrato Rodriguez', 'identification' => '1075601033', 'birth_date' => '2018-03-05', 'address' => 'La Esperanza', 'is_active' => true],
            ['name' => 'Brayan Jose Benavides Moncaleano', 'identification' => '1075600877', 'birth_date' => '2018-02-18', 'address' => 'La Esperanza', 'is_active' => true],
            ['name' => 'Jeidy Alexsandra Aldana Roman', 'identification' => '1215967679', 'birth_date' => '2018-09-23', 'address' => 'La Esperanza', 'is_active' => true],
            ['name' => 'Juan Alexis Pastor Ramirez', 'identification' => '1075600072', 'birth_date' => '2014-03-28', 'address' => 'Santana', 'is_active' => false],
            ['name' => 'Linda Valentina Guerrero Benavides', 'identification' => '1075600487', 'birth_date' => '2017-03-07', 'address' => 'La Esperanza', 'is_active' => true],
            ['name' => 'Laura Vanessa Guerrero Garcia', 'identification' => '1075599795', 'birth_date' => '2013-02-06', 'address' => 'Santana', 'is_active' => true],
            ['name' => 'Omaira Murcia Calvo', 'identification' => '1024548463', 'birth_date' => '2012-02-06', 'address' => 'Santana', 'is_active' => true],
            ['name' => 'Melanie Sofia Duran Amador', 'identification' => '1027288749', 'birth_date' => '2012-07-24', 'address' => 'Santa Ana', 'is_active' => true],
            ['name' => 'Jorge Ali Yanez Soto', 'identification' => '1067915948', 'birth_date' => '2010-04-21', 'address' => 'Calle 15 #43a-111 apartamento', 'is_active' => false],
            ['name' => 'Evelyn Sofía Méndez Gaona', 'identification' => '1074823846', 'birth_date' => '2013-02-23', 'address' => 'La Florida', 'is_active' => true],
            ['name' => 'Elkin Estiven Garcia Mayorga', 'identification' => '1013600399', 'birth_date' => '2006-02-06', 'address' => 'Palacio', 'is_active' => true],
            ['name' => 'Diana Marcela Torres Lozano', 'identification' => '1110117761', 'birth_date' => '2011-04-30', 'address' => 'San Isidro', 'is_active' => true],
            ['name' => 'Zanny Alexandra Lozano Sanchez', 'identification' => '1075600532', 'birth_date' => '2014-08-04', 'address' => 'Santa Ana', 'is_active' => true],
            ['name' => 'Karen Tatiana Villa Guerrero', 'identification' => '1078778101', 'birth_date' => '2012-12-15', 'address' => 'San Emilio', 'is_active' => true],
            ['name' => 'Nuvia Sirley Villa Guerrero', 'identification' => '1029886710', 'birth_date' => '2011-09-23', 'address' => 'San Emilio', 'is_active' => true],
            ['name' => 'Yury Melisa Ortigoza Matta', 'identification' => '1075506915', 'birth_date' => '2009-12-08', 'address' => 'San Marcos', 'is_active' => true],
        ];

        $created = 0;
        $updated = 0;

        foreach ($students as $studentData) {
            $student = User::where('identification', $studentData['identification'])->first();
            
            if (!$student) {
                User::create([
                    'name' => $studentData['name'],
                    'identification' => $studentData['identification'],
                    'email' => $this->generateEmail($studentData['name'], $studentData['identification']),
                    'password' => Hash::make($studentData['identification']),
                    'role' => 'student',
                    'birth_date' => $studentData['birth_date'],
                    'address' => $studentData['address'],
                    'is_active' => $studentData['is_active'],
                ]);
                $created++;
                $this->command->info("✓ Creado: {$studentData['name']} ({$studentData['identification']})");
            } else {
                $student->update([
                    'name' => $studentData['name'],
                    'birth_date' => $studentData['birth_date'],
                    'address' => $studentData['address'],
                    'is_active' => $studentData['is_active'],
                    'role' => 'student',
                ]);
                $updated++;
                $this->command->warn("↻ Actualizado: {$studentData['name']} ({$studentData['identification']})");
            }
        }

        $this->command->info("Total: {$created} creados, {$updated} actualizados");
    }

    private function generateEmail(string $name, string $identification): string
    {
        // Generar email basado en el nombre
        $parts = explode(' ', strtolower($name));
        $firstName = $parts[0] ?? 'estudiante';
        $lastName = $parts[count($parts) > 2 ? 2 : (count($parts) - 1)] ?? 'apellido';
        
        // Limpiar caracteres especiales
        $firstName = $this->cleanString($firstName);
        $lastName = $this->cleanString($lastName);
        
        return "{$firstName}.{$lastName}.{$identification}@estudiante.edu.co";
    }

    private function cleanString(string $string): string
    {
        $string = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ñ', 'ü'],
            ['a', 'e', 'i', 'o', 'u', 'n', 'u'],
            $string
        );
        return preg_replace('/[^a-z0-9]/', '', $string);
    }
}
