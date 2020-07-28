<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    {{-- page css --}}
    <link rel="stylesheet" href="{{ asset('assets') }}\vendors\datatables\dataTables.bootstrap.min.css">
    <link rel="stylesheet" href="{{ asset('assets') }}\vendors\yearpicker\yearpicker.css">
    <link rel="stylesheet" href="{{ asset('assets') }}/vendors/select2/select2.css">
    <link rel="stylesheet" href="{{ asset('assets') }}\vendors\tagify\tagify.css">

    {{-- required css --}}
    <link rel="stylesheet" href="{{ asset('assets') }}/css/app.min.css">

    <!-- custom css -->
    <style type="text/css">
        select#question_board{
            -webkit-appearance: none;
            -moz-appearance: none;
            text-indent: 1px;
            text-overflow: '';
        }
    </style>
    <!-- custom css -->

    {{-- required js --}}
    <script src="{{ asset('assets') }}/js/vendors.min.js"></script>
    <script src="{{ asset('assets') }}/js/app.min.js"></script>

    {{-- page js --}}<!-- page js -->
    <script src="{{ asset('assets') }}/vendors/select2/select2.min.js"></script>
    <script src="{{ asset('assets') }}/js/sweetalert.js"></script>
    <script src="{{ asset('assets') }}/vendors/yearpicker/yearpicker.js"></script>
    <script src="{{ asset('assets') }}\vendors\tagify\jQuery.tagify.min.js"></script>

    <title>ICT - Add Questions</title>
</head>
<body>
    <div class="navbar navbar-dark bg-dark navbar-expand">
        <h3 class="navbar-brand">Questions</h3>
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="#">
                <?php
                if (isset($_SESSION['login'])){
                    echo $_SESSION['login']['email'];
                } 
                ?>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{url('logout')}}">Logout</a>
            </li>
        </ul>
    </div>
    <div class="container ">
        
       

        {{-- question table --}} <hr>Total Question: <span id="total_question"></span><br>With Tag : <span id="with_tag_question">15</span>
                            
        <div class="card p-4" id="Question_table">

        </div>
    </div>
</div>
    {{-- modals start here --}}
    {{-- chapter name editation start modal --}}
    <div id="EditChapterNameModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Chapter Name</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <i class="anticon anticon-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <input id="HiddenEditChapterId" type="hidden" class="d-none">
                        <label for="EditChapterName">Chapter Name</label>
                        <input placeholder="Enter Chapter name" id="EditChapterName" type="text" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default m-r-10" data-dismiss="modal">Close</button>
                    <button id="UpdateChapterName" type="button" class="btn btn-primary" data-dismiss="modal">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    {{-- chapter name editation end modal --}}
    {{-- Topic editation start modal --}}
    <div id="EditTopicModal" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Update Topic Name or change Chapter</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <i class="anticon anticon-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <input id="HiddenEditTopicId" type="hidden" class="">
                    <div class="form-group">
                        <label for="EditTopicChapter">Select Chapter :</label>
                        <select id="EditTopicChapter" class="form-control"></select>
                    </div>
                    <div class="form-group">
                        <label for="EditTopicName">Topic Name :</label>
                        <input placeholder="Enter Topic name" id="EditTopicName" type="text" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default m-r-10" data-dismiss="modal">Close</button>
                    <button id="UpdateTopic" type="button" class="btn btn-primary" data-dismiss="modal">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    {{-- Topic editation end modal --}}
    {{-- show question start modal --}}
    <div class="modal fade" id="QuestionModal">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalScrollableTitle">Question no <span id="View_id"></span></h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <i class="anticon anticon-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="View_Chapter">Chapter</label>
                        <input type="text" class="form-control" id="View_Chapter" disabled>
                    </div>
                    <div class="form-group">
                        <label for="View_Topic">Topic</label>
                        <input type="text" class="form-control" id="View_Topic" disabled>
                    </div>
                    <div class="form-group">
                        <label for="View_Question">Question</label>
                        <input type="text" class="form-control" id="View_Question" disabled>
                    </div>
                    <div class="form-group">
                        <label for="View_Option1">Option 1</label>
                        <input type="text" class="form-control" id="View_Option1" disabled>
                    </div>
                    <div class="form-group">
                        <label for="View_Option2">Option 2</label>
                        <input type="text" class="form-control" id="View_Option2" disabled>
                    </div>
                    <div class="form-group">
                        <label for="View_Option3">Option 3</label>
                        <input type="text" class="form-control" id="View_Option3" disabled>
                    </div>
                    <div class="form-group">
                        <label for="View_Option4">Option 4</label>
                        <input type="text" class="form-control" id="View_Option4" disabled>
                    </div>
                    <div class="form-group">
                        <label for="View_Correct_Option">Correct Option</label>
                        <input type="text" class="form-control" id="View_Correct_Option" disabled>
                    </div>
                    <div class="form-group">
                        <label for="View_Detail">Detail</label>
                        <input type="text" class="form-control" id="View_Detail" disabled>
                    </div>
                    <div class="form-group">
                        <label for="View_Board">Board</label>
                        <input type="text" class="form-control" id="View_Board" disabled>
                    </div>
                    <div class="form-group">
                        <label for="View_Tag">Tag</label>
                        <input type="text" class="form-control" id="View_Tag" disabled>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    {{-- <button type="button" class="btn btn-primary">Save changes</button> --}}
                </div>
            </div>
        </div>
    </div>
    {{-- show question end modal --}}
    {{-- edit question start modal --}}
    <div class="modal fade" id="EditQuestionModal">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalScrollableTitle">Update Question</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <i class="anticon anticon-close"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="Edit_question_hidden_id">
                   
                    
                    <div class="form-group">
                        <label for="Edit_Question">Question :</label>
                        <input type="text" class="form-control" id="Edit_Question">
                    </div>
                    <div class="form-group">
                        <label for="Edit_Option1">Option 1 :</label>
                        <input type="text" class="form-control" id="Edit_Option1">
                    </div>
                    <div class="form-group">
                        <label for="Edit_Option2">Option 2 :</label>
                        <input type="text" class="form-control" id="Edit_Option2">
                    </div>
                    <div class="form-group">
                        <label for="Edit_Option3">Option 3 :</label>
                        <input type="text" class="form-control" id="Edit_Option3">
                    </div>
                    <div class="form-group">
                        <label for="Edit_Option4">Option 4 :</label>
                        <input type="text" class="form-control" id="Edit_Option4">
                    </div>
                    <div class="form-group">
                        <label for="Edit_Correct_Option">Correct Option</label>
                        <input type="text" class="form-control" id="Edit_Correct_Option">
                    </div>
                    <div class="form-group">
                        <label for="Edit_Detail">Detail :</label>
                        <input type="text" class="form-control" id="Edit_Detail">
                    </div>
                   
                    <div class="form-group">
                        <label for="Edit_Tag">Tag :</label>
                        <input type="text" class="form-control" id="Edit_Tag">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button id="UpdateQuestion" type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    {{-- edit question end modal --}}
    {{-- modal ends here --}}

    {{-- custom js --}}
    <script src="{{ asset('assets') }}/js/custom.js?{{time()}}"></script>
</body>
</html>
