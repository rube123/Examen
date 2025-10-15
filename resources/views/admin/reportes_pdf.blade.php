<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Películas</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f5f5f5; }
    </style>
</head>
<body>
    <h2>🎬 Reporte de películas más rentadas</h2>
    <table>
        <thead>
            <tr>
                <th>Título</th>
                <th>Total de rentas</th>
            </tr>
        </thead>
        <tbody>
            @foreach($topPeliculas as $p)
                <tr>
                    <td>{{ $p->title }}</td>
                    <td>{{ $p->total }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
