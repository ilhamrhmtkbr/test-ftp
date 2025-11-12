<?php

use ilhamrhmtkbr\App\Helper\StringHelper;
use ilhamrhmtkbr\App\Helper\TimeHelper;

?>
<!DOCTYPE html>
<html>

<head>
    <title>Laporan Company Office Financial Transactions</title>
    <style>
        :root {
            --bg-color: #ffffff;
            --second-bg-color: #fcfcfc;
            --third-bg-color: #fafafa;
            --fifth-bg-color: #f5f5f5;
            --text-color: #09090b;
            --border: 1px solid var(--border-color);
            --border-color: #ebebeb;
            --radius-m: .375rem;
        }

        @font-face {
            font-family: 'Regular';
            src: url('/public/assets/font/Poppins-Regular.ttf') format('truetype');
        }

        body {
            font-family: 'Regular', sans-serif;
        }

        table {
            font-size: 8pt;
            white-space: nowrap;
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }

        th {
            background-color: var(--third-bg-color);
            border-top: var(--border);
            border-bottom: var(--border);
        }

        th:first-child,
        td:first-child {
            border-left: var(--border);
            border-top-left-radius: var(--radius-m);
            border-bottom-left-radius: var(--radius-m);
        }

        th:last-child,
        td:last-child {
            border-right: var(--border);
            border-top-right-radius: var(--radius-m);
            border-bottom-right-radius: var(--radius-m);
        }

        th,
        td {
            padding: 7px 5px;
            vertical-align: middle;
        }

        tbody tr:hover {
            background-color: var(--second-bg-color);
        }

        tr:nth-child(even) {
            background-color: var(--third-bg-color);
        }

        td {
            cursor: pointer;
            border: none !important;
        }

        td.max-width {
            max-width: 100px;
            hyphens: auto;
            word-break: break-word;
            overflow-wrap: break-word;
            white-space: normal;
        }

        td:first-child {
            text-align: center;
        }

        @media print {
            table {
                border-collapse: collapse;
            }
        }

        .text-error-msg {
            font-weight: bold;
            color: var(--red-color);
        }
    </style>
</head>

<body>
    <h3>Laporan Company Office Financial Transactions</h3>
    <?php if (count($data['results']) > 0) : ?>
        <div class="table-box">
            <table>
                <thead>
                    <tr>
                        <th>No. </th>
                        <?php foreach (array_keys((array)$data['results'][0]) as $key): ?>
                            <?php if ($key == 'id') {
                                continue;
                            } ?>
                            <th><?= StringHelper::toCapitalize($key) ?></th>
                        <?php endforeach ?>
                    </tr>
                </thead>
                <tbody>
                    <?php $counter = 1;
                    foreach ($data['results'] as $items) : ?>
                        <tr>
                            <td><?= $counter++ ?></td>
                            <?php foreach ($items as $key => $value) : ?>
                                <?php if ($key == 'id') {
                                    continue;
                                } ?>
                                <td <?= ($key != 'name' || $key != 'description') ? "class='max-width'" : '' ?>
                                    <?= ($key == 'created_at' || $key == 'updated_at') ? "style='text-align:center;'" : '' ?>>
                                    <?php if ($key == 'created_at' || $key == 'updated_at') {
                                        echo TimeHelper::getTime($value);
                                    } else {
                                        echo $value;
                                    } ?>
                                </td>
                            <?php endforeach ?>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    <?php else : ?>
        <p class="text-error-msg">No result</p>
    <?php endif ?>
</body>

</html>