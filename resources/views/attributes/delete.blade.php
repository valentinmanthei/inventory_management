@extends ('layouts.app')
@section('content')
<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">
            Delete Category Attribute <small>{{$attribute->title}}</small>
        </h1>
    </div>
</div>
<!-- /.row -->

<div class="row">
    <div class="col-lg-12">
        <form role="form" method="post" action="{{route('attributes.delete', $attribute->id)}}">
            {{ csrf_field() }}
            <div class="form-group">
                <label>Are you sure you want to delete the Category Attribute {{$attribute->title}}?</label>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="confirm" value="yes"> YES
                    </label>
                </div>
            </div>
            <button type="submit" class="btn btn-default">Delete</button>
            <button type="reset" class="btn btn-default" onclick="window.history.back();">Back</button>

        </form>

    </div>
</div>
@endsection

@section('after-scripts')
@endsection