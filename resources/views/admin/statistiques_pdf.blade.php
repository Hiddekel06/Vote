<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques du Vote</title>
    <style>
        body {
            font-family: 'Garamond', 'Times New Roman', serif;
            color: #333;
            font-size: 12px;
            margin: 0;
        }
        @page {
            margin: 100px 40px 60px 40px;
        }
        header {
            position: fixed;
            top: -80px;
            left: 0;
            right: 0;
            height: 60px;
            width: 100%;
        }
        footer {
            position: fixed;
            bottom: -40px;
            left: 0;
            right: 0;
            height: 30px;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #888;
            border-top: 0.5px solid #ccc;
            padding-top: 5px;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }
        .header-table td {
            vertical-align: middle;
        }
        .header-table img {
            max-height: 60px;
            max-width: 60px;
        }
        .republic-name {
            font-weight: bold;
            font-size: 14px;
        }
        .motto {
            font-style: italic;
            font-size: 11px;
        }
        h1, h2 {
            font-weight: bold;
            margin-bottom: 0.8em;
            text-align: center;
        }
        h1 { font-size: 20px; text-transform: uppercase; margin-bottom: 1.5em; text-decoration: underline; }
        h2 { font-size: 16px; border-bottom: 1px solid #ccc; padding-bottom: 5px; margin-top: 2em; text-align: left; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1.5em;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 11px;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .text-bold {
            font-weight: bold;
        }
        .date-signature {
            margin-top: 40px;
            text-align: right;
        }
        .signature-box {
            margin-top: 10px;
            display: inline-block;
            text-align: center;
        }
    </style>
</head>
<body>
    <header>
        <table class="header-table">
            <tr>
                <td style="width: 25%; text-align: left;">
                    <img src="{{ public_path('images/logo_ministere.png') }}" alt="Logo Ministère">
                </td>
                <td style="width: 50%;">
                    <div class="republic-name">RÉPUBLIQUE DU SENEGAL</div>
                    <div class="motto">Un peuple Un But Une Foi</div>
                    <hr style="border: 0.5px solid #000; margin-top: 5px;">
                    <div>MINISTÈRE DE LA FONCTION PUBLIQUE</div>
                </td>
                <td style="width: 25%; text-align: right;">
                    <img src="{{ public_path('images/logo_communication.png') }}" alt="Logo Communication">
                </td>
            </tr>
        </table>
    </header>

    <footer>
        Généré par la plateforme GovAthon - {{ now()->format('d/m/Y H:i') }}
    </footer>

    <h1>Rapport Statistique des Votes</h1>

    <h2>Chiffres Clés</h2>
    <table>
        <thead>
            <tr>
                <th style="width: 60%;">Indicateur</th>
                <th style="width: 40%;" class="text-center">Valeur</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total des Votes</td>
                <td class="text-center text-bold">{{ $totalVotes }}</td>
            </tr>
            <tr>
                <td>Projets Participants</td>
                <td class="text-center text-bold">{{ $totalProjets }}</td>
            </tr>
            <tr>
                <td>Projet en Tête</td>
                <td class="text-center">{{ $projetGagnant ? $projetGagnant->nom_projet . ' (' . $projetGagnant->votes_count . ' votes)' : 'N/A' }}</td>
            </tr>
        </tbody>
    </table>

    <h2>Répartition des Votes par Secteur</h2>
    <table>
        <thead>
            <tr>
                <th>Secteur</th>
                <th class="text-center">Nombre de Votes</th>
                <th class="text-center">Pourcentage du Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($votesParSecteur as $secteur)
                <tr>
                    <td>{{ $secteur->nom }}</td>
                    <td class="text-center">{{ $secteur->total_votes }}</td>
                    <td class="text-center">{{ $totalVotes > 0 ? number_format(($secteur->total_votes / $totalVotes) * 100, 2) : '0.00' }} %</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Classement des Projets</h2>
    <table>
        <thead>
            <tr>
                <th class="text-center">Rang</th>
                <th>Nom du Projet</th>
                <th>Équipe</th>
                <th>Secteur</th>
                <th class="text-center">Nombre de Votes</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($projets as $index => $projet)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $projet->nom_projet }}</td>
                    <td>{{ $projet->nom_equipe }}</td>
                    <td>{{ $projet->secteur->nom ?? 'N/A' }}</td>
                    <td class="text-center">{{ $projet->votes_count }}</td>
                </tr>
            @empty
                <tr><td colspan="5" style="text-align: center;">Aucun projet participant.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="date-signature">
        Fait à Yonville, le {{ now()->format('d/m/Y') }}.
        <div class="signature-box">
            <p>Pour le comité d'organisation,</p>
            <br><br><br>
            <p>(Signature et Cachet)</p>
        </div>
    </div>
</body>
</html>