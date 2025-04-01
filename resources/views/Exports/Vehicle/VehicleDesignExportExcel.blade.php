<div>
    <table style="border: 0.5px solid black; border-collapse: collapse;">
        <thead>
            <tr>
                <th colspan="2"></th>
                <th colspan="3" style="padding: 10px;">
                    <div style="display: flex; justify-content: center; align-items: center; text-align: center;">
                        <img src="https://api.inspecciones.desarrollo.com.co/storage/companies/company_9e21d8d4-e4fc-410b-b730-853b536b1634/en9ZVxNUhRGB1omZsvlnHfqSKTvhhD5PXi3GVxA4.png"
                            alt="Logo" width="45" height="45">
                    </div>
                </th>

                <th colspan="31" style="text-align: center;font-weight: bold;">
                    SISTEMA DE GESTIÓN HSEQ

                    <br />

                    INSPECCIÓN PREOPERACIONAL SISTEMAS DE VACIO, MANGUERAS Y ACOPLES OPERACIÓN PERENCO
                </th>
            </tr>

            <tr>
                <th colspan="5" style="text-align: center;font-weight: bold;background: gray;">
                    Código:
                </th>
                <th colspan="10" style="text-align: center;">
                    Fecha:
                </th>
                <th colspan="10" style="text-align: center;">
                    Versión:
                </th>
                <th colspan="11" style="text-align: center;font-weight: bold;background: gray;">
                    Página:
                </th>
            </tr>

            <tr>
                <th colspan="5">
                    No. Identificacion:
                </th>
                <th colspan="10">
                    Tipo de Manguera:
                </th>
                <th colspan="10">
                    Longitud:
                </th>
                <th colspan="11">
                    Tipo de Manguera:
                </th>
            </tr>

            <tr>
                <th colspan="5">
                    Mes y año de la inspeccion: {{ $data['month'] }} - {{ $data['year'] }}
                </th>
                <th colspan="15">
                    Nombre del operador:
                </th>
                <th colspan="16">
                    Placa Vehiculo: {{ $data['license_plate'] }}
                </th>
            </tr>

            <tr>
                <th colspan="5" style="text-align: center;font-weight: bold;">
                    SISTEMAS DE VACIO
                </th>
                <th colspan="31" style="text-align: center;font-weight: bold;">
                    DIA
                </th>
            </tr>

            <tr>
                <th colspan="3" style="text-align: center;font-weight: bold;">
                    BOMBA TRIPLEX
                </th>
                <th colspan="1" style="text-align: center;font-weight: bold;">
                    APLICA
                </th>
                <th colspan="1" style="text-align: center;font-weight: bold;">
                    NO APLICA
                </th>

                {{-- Dias --}}

                @for ($i = 1; $i <= $data['days']; $i++)
                    <th colspan="1" style="text-align: center;font-weight: bold;">{{ $i }}</th>
                @endfor
            </tr>

            <tr>
                <th colspan="5">
                    NIVELES DE VALVULINA DE BOMBA TRIPLEX
                </th>
            </tr>

            <tr>
                <th colspan="5">
                    NOMBRE OPERADOR DE ACUERDO A ASIGNACION DIARIA
                </th>

                @for ($i = 1; $i <= $data['days']; $i++)
                    <th colspan="1" style="text-align: center;font-weight: bold;">

                    </th>
                @endfor
            </tr>

            <tr>
                <th colspan="36">

                </th>
            </tr>

            <tr>
                <th colspan="36">
                    Califique B (Bueno), M (Malo), NA (No Aplica)
                </th>
            </tr>
            <tr>
                <th colspan="36">
                    OBSERVACIONES
                </th>
            </tr>
        </thead>
    </table>
</div>
