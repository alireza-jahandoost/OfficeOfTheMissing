<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h1>دفتر گمشدگان</h1>

    <p>فردی ادعا کرده است که صاحب مدرک پیدا شده توسط شما است</p>
    <p>لطفا اطلاعات پر شده توسط این فرد را بررسی نمایید. در صورتی که اطلاعات همخوانی ندارد از این ایمیل صرف نظر کنید و در غیر این صورت میتوانید از طریق راه های ارتباطی با گم کننده در تماس باشید</p>

    @foreach($lost->properties as $property)
        <div>
            <span>{{$property->propertyType->name}}</span>
            <span> : </span>
            @if($property->propertyType->value_type === 'text')
                <span>{{$property->value}}</span>
            @else
                <img
                    src="http://localhost:8000/{{$property->value}}"
                    alt="{{$property->propertyType->name}}"
                />
            @endif
        </div>
    @endforeach

    <div>
        <h3>اطلاعات تماس: </h3>
        <div>
            <span>ایمیل : </span>
            <span>{{$lost->user->email}}</span>
        </div>
    </div>
</body>
</html>
