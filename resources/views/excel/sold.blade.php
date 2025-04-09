<table>
    <thead>
        <tr>
            <th colspan="6" style="text-align:center;font-weight:bold;font-size:14pt;">REPORTE DE VENTAS AL
                {{ $datetime }}</th>
        </tr>
        <tr>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td style="font-weight:bold;">#</td>
            <td style="font-weight:bold;">Campaña</td>
            <td style="font-weight:bold;">Pestaña</td>
            <td style="font-weight:bold;">Estado</td>
            <td style="font-weight:bold;">Fecha creación</td>
            <td style="font-weight:bold;">Usuario</td>

        </tr>
        @foreach ($solds as $element)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $element->campain_name }}</td>
                <td>{{ $element->tab_state_name }}</td>
                <td>{{ $element->state_name }}</td>
                <td>{{ $element->form_created_at }}</td>
                <td>{{ $element->created_at_user }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
