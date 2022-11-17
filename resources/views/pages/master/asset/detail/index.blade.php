<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <fieldset>
        <legend>Asset Detail</legend>
        <table>
            <tr>
                <td>ID</td>
                <td>:</td>
                <td>{{ $asset->asset_id }}</td>
            </tr>
            <tr>
                <td>Name</td>
                <td>:</td>
                <td>{{ $asset->asset_name }}</td>
            </tr>
            <tr>
                <td>Merk / Type</td>
                <td>:</td>
                <td>{{ $asset->asset_merktype }}</td>
            </tr>
            <tr>
                <td>Bought Date</td>
                <td>:</td>
                <td>{{ $asset->asset_dateachieved }}</td>
            </tr>
            <tr>
                <td>Amount</td>
                <td>:</td>
                <td>{{ $asset->asset_amount }}</td>
            </tr>
            <tr>
                <td>Running Stock</td>
                <td>:</td>
                <td>{{ $asset->asset_running_stock }}</td>
            </tr>
            <tr>
                <td>Unit</td>
                <td>:</td>
                <td>{{ $asset->asset_unit }}</td>
            </tr>
            <tr>
                <td>Condition</td>
                <td>:</td>
                <td>{{ $asset->asset_condition }}</td>
            </tr>
            <tr>
                <td>Notes</td>
                <td>:</td>
                <td>{{ $asset->asset_notes }}</td>
            </tr>
            <tr>
                <td>Status</td>
                <td>:</td>
                <td>{{ $asset->asset_status }}</td>
            </tr>
        </table>
    </fieldset>

    <fieldset>
        <legend>Used by</legend>
        <table>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Amount</th>
                <th>Start Used</th>
                <th>End Used</th>
                <th>Condition</th>
                <th>Status</th>
            </tr>
            @php
                $no = 1;
            @endphp
            @foreach ($asset->user as $user)
                <tr>
                    <td>{{ $no++ }}</td>
                    <td>{{ $user->first_name.' '.$user->last_name }}</td>
                    <td>{{ $user->pivot->amount_used }}</td>
                    <td>{{ $user->pivot->start_used }}</td>
                    <td>{{ $user->pivot->end_used ?? '-' }}</td>
                    <td>{{ $user->pivot->condition ?? '-' }}</td>
                    <td>{{ $user->pivot->status == 0 ? "used" : "unused" }}</td>
                </tr>
            @endforeach
        </table>
    </fieldset>
</body>
</html>