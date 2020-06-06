

<div class="form-group">

    {!! Form::open(['method'=> 'GET',
               'action'=>'GameController@add']) !!}


    {!! Form::label('game', 'Choose a Game:') !!}
    {!! Form::select('game', $select, ['class'=>'form-control']) !!}

    <br>

    {!! Form::label('armies', 'Add army:') !!}<br>
    {!! Form::label('army_name', 'Army name:') !!}
    {!! Form::text('army_name'); !!}
    {!! Form::label('units_number', 'Number of units:') !!}
    {!! Form::text('units_number'); !!}
    {!! Form::label('strategy', 'Attack strategy:') !!}
    {!! Form::text('strategy'); !!}
    {!! Form::submit('Add Army') !!}

    {!! Form::close() !!}


</div>

<div class="form-group">

    {!! Form::open(['method'=> 'GET',
               'action'=>'GameController@attack']) !!}


    {!! Form::label('game', 'Choose a Game:') !!}
    {!! Form::select('game', $select, ['class'=>'form-control']) !!}

    <br>


    {!! Form::submit('Start Fight!') !!}

    {!! Form::close() !!}


</div>
