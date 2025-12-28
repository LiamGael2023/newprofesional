<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Pago - <?= e($pago['numero_recibo']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/@tabler/core@1.0.0-beta17/dist/css/tabler.min.css" rel="stylesheet"/>
    <style>
        @media print {
            .no-print { display: none; }
            body { margin: 0; padding: 20px; }
        }
        body {
            background: #f5f5f5;
            padding: 20px;
        }
        .recibo-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border: 2px solid #000;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
        }
        .recibo-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .section {
            margin-bottom: 20px;
        }
        .section h3 {
            background: #000;
            color: white;
            padding: 10px;
            margin: 0 0 10px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        table th {
            background: #f0f0f0;
            font-weight: bold;
        }
        .total-row {
            font-weight: bold;
            font-size: 18px;
            background: #f0f0f0;
        }
        .firma {
            margin-top: 60px;
            text-align: center;
        }
        .firma-line {
            border-top: 2px solid #000;
            width: 300px;
            margin: 0 auto;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" class="btn btn-primary">
            <i class="ti ti-printer"></i> Imprimir Recibo
        </button>
        <button onclick="window.close()" class="btn btn-secondary">
            Cerrar
        </button>
    </div>

    <div class="recibo-container">
        <!-- Header -->
        <div class="header">
            <h1>COLEGIO PROFESIONAL</h1>
            <p>RUC: 20123456789</p>
            <p>Dirección: Av. Principal 123, Lima - Perú</p>
            <p>Teléfono: (01) 123-4567 | Email: info@colegio.pe</p>
        </div>

        <!-- Recibo Info -->
        <div class="recibo-info">
            <div>
                <strong>RECIBO DE PAGO</strong><br>
                <strong>N°:</strong> <?= e($pago['numero_recibo']) ?>
            </div>
            <div style="text-align: right;">
                <strong>Fecha:</strong> <?= formatDateTime($pago['fecha_pago']) ?><br>
                <strong>Usuario:</strong> <?= e($pago['usuario_nombre']) ?>
            </div>
        </div>

        <!-- Datos del Colegiado -->
        <div class="section">
            <h3>DATOS DEL COLEGIADO</h3>
            <table>
                <tr>
                    <td width="25%"><strong>Código:</strong></td>
                    <td width="25%"><?= e($pago['codigo_colegiado']) ?></td>
                    <td width="25%"><strong>DNI:</strong></td>
                    <td width="25%"><?= e($pago['dni']) ?></td>
                </tr>
                <tr>
                    <td><strong>Nombre:</strong></td>
                    <td colspan="3"><?= e($pago['nombre_completo']) ?></td>
                </tr>
                <tr>
                    <td><strong>Email:</strong></td>
                    <td><?= e($pago['email']) ?></td>
                    <td><strong>Celular:</strong></td>
                    <td><?= e($pago['celular']) ?></td>
                </tr>
            </table>
        </div>

        <!-- Detalle del Pago -->
        <div class="section">
            <h3>DETALLE DEL PAGO</h3>
            <table>
                <thead>
                    <tr>
                        <th>Concepto</th>
                        <th>Periodo</th>
                        <th>Monto</th>
                        <th>Mora</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($detalles as $detalle): ?>
                    <tr>
                        <td><?= e($detalle['tipo_nombre']) ?></td>
                        <td><?= e($detalle['periodo']) ?></td>
                        <td>S/ <?= number_format($detalle['monto'], 2) ?></td>
                        <td>S/ <?= number_format($detalle['mora'], 2) ?></td>
                        <td>S/ <?= number_format($detalle['monto_pagado'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="total-row">
                        <td colspan="4" style="text-align: right;">TOTAL PAGADO:</td>
                        <td>S/ <?= number_format($pago['monto_total'], 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Método de Pago -->
        <div class="section">
            <h3>INFORMACIÓN DE PAGO</h3>
            <table>
                <tr>
                    <td width="30%"><strong>Método de Pago:</strong></td>
                    <td><?= e($pago['metodo_pago']) ?></td>
                </tr>
                <?php if (!empty($pago['referencia'])): ?>
                <tr>
                    <td><strong>Referencia/Comprobante:</strong></td>
                    <td><?= e($pago['referencia']) ?></td>
                </tr>
                <?php endif; ?>
                <?php if (!empty($pago['observaciones'])): ?>
                <tr>
                    <td><strong>Observaciones:</strong></td>
                    <td><?= e($pago['observaciones']) ?></td>
                </tr>
                <?php endif; ?>
            </table>
        </div>

        <!-- Firma -->
        <div class="firma">
            <div class="firma-line">
                Firma y Sello del Cajero
            </div>
        </div>

        <!-- Footer -->
        <div style="margin-top: 30px; text-align: center; font-size: 12px; color: #666;">
            <p>Este documento es un comprobante de pago válido.</p>
            <p>Conserve este recibo para cualquier reclamo o consulta.</p>
            <p>Generado el <?= date('Y-m-d H:i:s') ?></p>
        </div>
    </div>
</body>
</html>
