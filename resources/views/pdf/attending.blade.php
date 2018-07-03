<!DOCTYPE html>
<html>
<head>
	<title>User list - PDF</title>
	
</head>
<body style="font-family:sans-serif">
    <div style="background-color:white;">
        <div style="background-color:#fafafa;border: 1px solid #ddd;">
            <div style="min-width:90%;color:#fff;background-color:#384148;padding:20px 0">
                <span style="display:inline-block;width:3%;margin:0;padding-left:20px">#</span>
                <span style="display:inline-block;width:18%;margin:0;padding-left:20px">Register Number</span>
                <span style="display:inline-block;width:30%;margin:0;padding-left:20px">Name</span>
                <span style="display:inline-block;width:35%;margin:0;padding-left:20px">Email</span>
            </div>
            <div>
                @foreach ($students as $key => $value)
                <div style="margin:0;padding-bottom:10px;padding-top:15px;border-bottom: 2px solid #ddd">
                    <span style="display:inline-block;width:5%;margin:0;padding-left:20px;">{{ $key + 1 }}</span>
                    <span style="display:inline-block;width:15%;margin:0;padding-left:20px;">{{ $value->student_id }}</span>
                    <span style="display:inline-block;width:30%;margin:0;padding-left:20px;">{{ $value->name }}</span>
                    <span style="display:inline-block;width:35%;margin:0;padding-left:20px;">{{ $value->email }}</span>
                </div>
                    @if(($key+1) % 19 == 0)
                    <div style="page-break-before: always;"></div>
                    <div style="min-width:90%;color:#fff;background-color:#384148;padding:20px 0">
                        <span style="display:inline-block;width:3%;margin:0;padding-left:20px">#</span>
                        <span style="display:inline-block;width:18%;margin:0;padding-left:20px">Register Number</span>
                        <span style="display:inline-block;width:30%;margin:0;padding-left:20px">Name</span>
                        <span style="display:inline-block;width:35%;margin:0;padding-left:20px">Email</span>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</body>
</html>