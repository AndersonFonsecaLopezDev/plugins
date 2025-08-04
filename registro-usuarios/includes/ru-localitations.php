<?php
// Retorna un array con todos los departamentos de Colombia
function ru_get_departamentos_colombia() {
    return [
        'Amazonas', 'Antioquia', 'Arauca', 'Atlántico', 'Bolívar', 'Boyacá', 'Caldas', 'Caquetá', 'Casanare', 'Cauca', 'Cesar', 'Chocó', 'Córdoba', 'Cundinamarca', 'Guainía', 'Guaviare', 'Huila', 'La Guajira', 'Magdalena', 'Meta', 'Nariño', 'Norte de Santander', 'Putumayo', 'Quindío', 'Risaralda', 'San Andrés y Providencia', 'Santander', 'Sucre', 'Tolima', 'Valle del Cauca', 'Vaupés', 'Vichada'
    ];
}

// Retorna un array asociativo de ciudades agrupadas por departamento
function ru_get_ciudades_colombia() {
    return [
        'Amazonas' => ['Leticia', 'Puerto Nariño'],
        'Antioquia' => ['Medellín', 'Bello', 'Itagüí', 'Envigado', 'Apartadó', 'Turbo', 'Rionegro', 'La Ceja', 'Caucasia', 'Sabaneta', 'Copacabana', 'Girardota', 'Marinilla', 'Yarumal', 'Santa Fe de Antioquia'],
        'Arauca' => ['Arauca', 'Arauquita', 'Saravena', 'Tame'],
        'Atlántico' => ['Barranquilla', 'Soledad', 'Malambo', 'Sabanalarga', 'Baranoa', 'Galapa', 'Puerto Colombia'],
        'Bolívar' => ['Cartagena', 'Magangué', 'El Carmen de Bolívar', 'Turbaco', 'Arjona', 'San Juan Nepomuceno'],
        'Boyacá' => ['Tunja', 'Duitama', 'Sogamoso', 'Chiquinquirá', 'Paipa', 'Villa de Leyva'],
        'Caldas' => ['Manizales', 'La Dorada', 'Villamaría', 'Chinchiná', 'Riosucio'],
        'Caquetá' => ['Florencia', 'Belén de los Andaquíes', 'San Vicente del Caguán'],
        'Casanare' => ['Yopal', 'Aguazul', 'Villanueva', 'Tauramena'],
        'Cauca' => ['Popayán', 'Santander de Quilichao', 'Puerto Tejada', 'Patía'],
        'Cesar' => ['Valledupar', 'Aguachica', 'Codazzi', 'La Jagua de Ibirico'],
        'Chocó' => ['Quibdó', 'Istmina', 'Tadó', 'Condoto'],
        'Córdoba' => ['Montería', 'Lorica', 'Sahagún', 'Cereté'],
        'Cundinamarca' => ['Soacha', 'Facatativá', 'Zipaquirá', 'Girardot', 'Chía', 'Fusagasugá', 'Madrid', 'Mosquera', 'La Calera'],
        'Guainía' => ['Inírida'],
        'Guaviare' => ['San José del Guaviare'],
        'Huila' => ['Neiva', 'Pitalito', 'Garzón', 'La Plata'],
        'La Guajira' => ['Riohacha', 'Maicao', 'Uribia', 'Fonseca'],
        'Magdalena' => ['Santa Marta', 'Ciénaga', 'Fundación', 'El Banco'],
        'Meta' => ['Villavicencio', 'Acacías', 'Granada', 'Puerto López'],
        'Nariño' => ['Pasto', 'Tumaco', 'Ipiales', 'Túquerres'],
        'Norte de Santander' => ['Cúcuta', 'Ocaña', 'Pamplona', 'Villa del Rosario'],
        'Putumayo' => ['Mocoa', 'Puerto Asís', 'Orito'],
        'Quindío' => ['Armenia', 'Calarcá', 'Montenegro', 'La Tebaida'],
        'Risaralda' => ['Pereira', 'Dosquebradas', 'La Virginia', 'Santa Rosa de Cabal'],
        'San Andrés y Providencia' => ['San Andrés', 'Providencia'],
        'Santander' => ['Bucaramanga', 'Floridablanca', 'Girón', 'Piedecuesta', 'Barrancabermeja', 'Socorro', 'San Gil'],
        'Sucre' => ['Sincelejo', 'Corozal', 'Sampués', 'San Marcos'],
        'Tolima' => ['Ibagué', 'Espinal', 'Melgar', 'Honda'],
        'Valle del Cauca' => ['Cali', 'Palmira', 'Buenaventura', 'Tuluá', 'Buga', 'Cartago', 'Jamundí', 'Yumbo'],
        'Vaupés' => ['Mitú'],
        'Vichada' => ['Puerto Carreño']
    ];
} 