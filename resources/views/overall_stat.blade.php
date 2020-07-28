<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="{{asset('asset')}}/bs5/bootstrap.min.css" integrity="sha384-r4NyP46KrjDleawBgD5tp8Y7UzmLA05oM1iAEQ17CSuDqnUK2+k9luXQOfXJCJ4I" crossorigin="anonymous">
    <style>
    body{
      background: url({{asset('asset')}}/background_image.png) no-repeat center center fixed;
      background-color: rgba(255,255,255,0.8);
      background-blend-mode: lighten;
      -webkit-background-size: cover;
      -moz-background-size: cover;
      -o-background-size: cover;
      background-size: cover;
    }
    </style>
    <title>Hello, world!</title>
  </head>
  <body>
    <div class="container">
   
          <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show active" id="Math" role="tabpanel" aria-labelledby="Math-tab">
              <table class="table table-responsive table-borderless my-3">
                <thead>
                  <th>Subject</th>
                  <th>Progress</th>
                </thead>
                <tbody>
                  
                 @foreach($subjects as $subject)
                    <tr>
                      <td>{{$subject->subject_name}}</td></td>
                      <td class="">
                        <div class="progress">
                          <div class="progress-bar progress-bar-striped bg-warning" role="progressbar" style="width: {{$subject->percentage}}%" aria-valuenow="{{$subject->percentage}}" aria-valuemin="0" aria-valuemax="100">{{$subject->percentage}}%</div>
                        </div>
                      </td>
                    </tr>
                    @endforeach

                </tbody>
              </table>
            </div>
            <div class="tab-pane fade" id="ICT" role="tabpanel" aria-labelledby="ICT-tab">
              <table class="table table-responsive table-borderless my-3">
                <thead>
                  <th>Marks</th>
                  <th>Progress</th>
                </thead>
                <tbody>
                   @foreach($subjects as $subject)
                    <tr>
                      <td>{{$subject->subject_name}}</td></td>
                      <td class="">
                        <div class="progress">
                          <div class="progress-bar progress-bar-striped bg-warning" role="progressbar" style="width: {{$subject->percentage}}%" aria-valuenow="{{$subject->percentage}}" aria-valuemin="0" aria-valuemax="100">{{$subject->percentage}}%</div>
                        </div>
                      </td>
                    </tr>
                    @endforeach

                </tbody>
              </table>
            </div>
          </div>
    </div>

    <!-- Optional JavaScript -->
    <!-- Popper.js first, then Bootstrap JS -->
    <script src="bs5/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="bs5/bootstrap.min.js" integrity="sha384-oesi62hOLfzrys4LxRF63OJCXdXDipiYWBnvTl9Y9/TRlw5xlKIEHpNyvvDShgf/" crossorigin="anonymous"></script>
  </body>
</html>