<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Appointment Notification</title>
    <style>
        body {
            font-family: 'Georgia', serif;
            background-color: #0c0c0c;
            color: #ffffff;
            margin: 0;
            padding: 40px 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #111111;
            border: 1px solid rgba(255, 255, 255, 0.05);
            padding: 40px;
            border-radius: 8px;
        }
        .header {
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            letter-spacing: 0.2em;
            text-transform: uppercase;
            color: #c5a059;
            font-weight: bold;
        }
        .title {
            font-size: 20px;
            font-style: italic;
            margin-top: 10px;
            color: #ffffff;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            border-radius: 4px;
            margin-bottom: 25px;
        }
        .status-booked {
            background-color: rgba(197, 160, 89, 0.1);
            color: #c5a059;
            border: 1px solid rgba(197, 160, 89, 0.2);
        }
        .status-updated {
            background-color: rgba(59, 130, 246, 0.1);
            color: #60a5fa;
            border: 1px solid rgba(59, 130, 246, 0.2);
        }
        .status-cancelled {
            background-color: rgba(239, 68, 68, 0.1);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .details-table td {
            padding: 12px 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.02);
            font-size: 14px;
        }
        .label {
            color: #888888;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            width: 30%;
        }
        .value {
            color: #ffffff;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            color: #555555;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            border-top: 1px solid rgba(255, 255, 255, 0.05);
            padding-top: 20px;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">Jcob</div>
            <div class="title">The Art of Grooming</div>
        </div>

        <div style="text-align: center;">
            @if($action === 'booked')
                <span class="status-badge status-booked">New Booking Confirmation</span>
            @elseif($action === 'updated')
                <span class="status-badge status-updated">Appointment Rescheduled</span>
            @else
                <span class="status-badge status-cancelled">Appointment Cancelled</span>
            @endif
        </div>

        <table class="details-table">
            <tr>
                <td class="label">Customer</td>
                <td class="value">{{ $appointment->customer_name }}</td>
            </tr>
            <tr>
                <td class="label">Phone</td>
                <td class="value">{{ $appointment->customer_phone }}</td>
            </tr>
            <tr>
                <td class="label">Service</td>
                <td class="value">{{ $appointment->service }}</td>
            </tr>
            <tr>
                <td class="label">Date & Time</td>
                <td class="value">{{ \Carbon\Carbon::parse($appointment->start_at)->format('Y-m-d H:i') }}</td>
            </tr>
            <tr>
                <td class="label">Barber</td>
                <td class="value">{{ $appointment->barber ? $appointment->barber->name : 'Unassigned' }}</td>
            </tr>
            @if($action === 'updated')
                <tr>
                    <td class="label">Status</td>
                    <td class="value" style="color: #60a5fa; text-transform: uppercase; font-size: 12px;">{{ $appointment->status }}</td>
                </tr>
            @endif
        </table>

        <div class="footer">
            &copy; {{ date('Y') }} Jcob Barbershop. All rights reserved.
        </div>
    </div>
</body>
</html>
