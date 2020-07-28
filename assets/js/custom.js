$(function (){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    }); // csrf solution
    $(".yearpicker").yearpicker(); // year picker for add board
    $('.select2').select2();
    $("#question_tag").tagify();

    // add question - board list js
    $('#addMore').on('click', function() {
        $(".boards:last-child").clone(true).appendTo("#board_field");
    });
    $(document).on('click', '.remove', function() {
        var trIndex = $(this).closest("div.boards").index();
        if(trIndex>1) {
            $(this).closest("div.boards").remove();
        } else {
            alert("Sorry!! Can't remove first input!");
        }
    });
    // add question - board list js

    // adding question
    $("#AddQuestion").click(function() {
        
        var question_tag = $("#question_tag").val();
        question_tag = JSON.parse(question_tag);
        
        
        var loop_count = Object.keys(question_tag).length;
        var i;
        var final_tag = '';
        for (i=0 ; i<loop_count; i++)
        {
            final_tag +=question_tag[i].value+",";
        }
        final_tag = final_tag.replace(/,\s*$/, ""); //remove last commma;
        //alert(final_tag);
  
                
     
         
        if (
        // $("#question_topic").val().length !== 0 &&
        $("#question").val().length !== 0 &&
        $("#question_option1").val().length !== 0 &&
        $("#question_option2").val().length !== 0 &&
        $("#question_option3").val().length !== 0 &&
        $("#question_option4").val().length !== 0 &&
        $("#question_correct_option").val().length !== 0
        ){
            if ( 1 == 1
                // $("#question_option1").val() === $("#question_correct_option").val() ||
                // $("#question_option2").val() === $("#question_correct_option").val() ||
                // $("#question_option3").val() === $("#question_correct_option").val() ||
                // $("#question_option4").val() === $("#question_correct_option").val()
            ){
                var board_list = [];
                $('#question_board').each(function(){
                    board_list.push($(this).val());
                });
                
                
                

                var formdata = new FormData();
                // formdata.append('topic_id',$("#question_topic").val());
                formdata.append('question',$("#question").val());
                formdata.append('board_id',board_list);
                formdata.append('option1',$("#question_option1").val());
                formdata.append('option2',$("#question_option2").val());
                formdata.append('option3',$("#question_option3").val());
                formdata.append('option4',$("#question_option4").val());
                formdata.append('correct_option',$("#question_correct_option").val());
                formdata.append('tag',final_tag);
                formdata.append('details',$("#question_answer_detail").val());
                $.ajax({
                    processData:false,
                    contentType:false,
                    data:formdata,
                    type:"post",
                    url:"AddQuestion",
                    success:function(data) {
                        $("#question_topic").val("");
                        $("#question_board").val("");
                        $("#question_board").trigger("change");
                        $("#question").val("");
                        $("#question_option1").val("");
                        $("#question_option2").val("");
                        $("#question_option3").val("");
                        $("#question_option4").val("");
                        $("#question_correct_option").val("");
                       
                        $("#question_answer_detail").val("");
                        location.reload();
                        //Show_Question();
                    }
                });
                // alert($("#question_topic").val()+" "+
                // board_list+" "+
                // $("#question").val()+" "+
                // $("#question_option1").val()+" "+
                // $("#question_option2").val()+" "+
                // $("#question_option3").val()+" "+
                // $("#question_option4").val()+" "+
                // $("#question_correct_option").val()+" "+
                // $("#question_tag").val()+" "+
                // $("#question_answer_detail").val());
            }
            else{
                alert("correct option missmathed");
            }
        }
        else{
            alert("Fill the required inputs");
        }
    });




    // adding chapters
    $("#AddChapter").click(function(){
        if ($("#ChapterName").val().length !== 0) {
            var formdata = new FormData();
            formdata.append('chapter_name',$("#ChapterName").val());
            $.ajax({
                processData:false,
                contentType:false,
                data:formdata,
                type:"post",
                url:"AddChapter",
                success:function(data) {
                    $("#ChapterName").val("");
                    // alert(data);
                    show_chapter_list();
                }
            });
        }
        else{
            alert("Chapter field is empty");
        }
    });

    // adding topic
    $("#AddTopics").click(function (){
        if ($("#Topic-name").val().length !== 0 && $("#Topic-chapter").val().length) {
            var formdata = new FormData();
            formdata.append('chapter_id',$("#Topic-chapter").val());
            formdata.append('topic_name',$("#Topic-name").val());
            $.ajax({
                processData:false,
                contentType:false,
                data:formdata,
                type:"post",
                url:"AddTopic",
                success:function(data) {
                    $("#Topic-name").val("");
                    $("#Topic-chapter").val("");
                    // alert(data);
                    show_topics();
                }
            });
        }
        else{
            alert("Inputs Empty");
        }
    });

    // adding board
    $("#AddBoard").click(function (){
        if ($("#board_name").val().length !== 0 && $("#board_year").val().length) {
            var formdata = new FormData();
            formdata.append('board_name',$("#board_name").val());
            formdata.append('year',$("#board_year").val());
            $.ajax({
                processData:false,
                contentType:false,
                data:formdata,
                type:"post",
                url:"AddBoard",
                success:function(data) {
                    $("#board_name").val("");
                    $("#board_year").val("");
                    // alert(data);
                    show_board();
                }
            });
        }
        else{
            alert("Inputs Empty");
        }
    });


    // update functions

    // update question
    $("#UpdateQuestion").click(function() {
        if ($("#Edit_question_hidden_id").val().length !== 0 &&
        
        $("#Edit_Option1").val().length !== 0 &&
        $("#Edit_Option2").val().length !== 0 &&
        $("#Edit_Option3").val().length !== 0 &&
        $("#Edit_Option4").val().length !== 0 &&
        $("#Edit_Correct_Option").val().length !== 0) {
            if (
                1==1
                // $("#Edit_Option1").val() === $("#Edit_Correct_Option").val() ||
                // $("#Edit_Option2").val() === $("#Edit_Correct_Option").val() ||
                // $("#Edit_Option3").val() === $("#Edit_Correct_Option").val() ||
                // $("#Edit_Option4").val() === $("#Edit_Correct_Option").val()
            ) {
        var formdata = new FormData();
            formdata.append('id',$("#Edit_question_hidden_id").val());
            
            formdata.append('question',$("#Edit_Question").val());
            formdata.append('option1',$("#Edit_Option1").val());
            formdata.append('option2',$("#Edit_Option2").val());
            formdata.append('option3',$("#Edit_Option3").val());
            formdata.append('option4',$("#Edit_Option4").val());
            formdata.append('correct_answer',$("#Edit_Correct_Option").val());
            
       
            formdata.append('tag',$("#Edit_Tag").val());
            $.ajax({
                processData:false,
                contentType:false,
                data:formdata,
                type:"post",
                url:"UpdateQuestion",
                success:function(data) {
                    $("#EditQuestionModal").modal('hide');
                    
                    Show_Question();
                }
            });

        }
        else{
            alert("Correct option missmathed!");
        }
        }
        else{
            $("#EditQuestionModal").modal('hide');
            alert("Riquired field was empty, Not updated!");
        }
    });

    // cahapter update
    $("#UpdateChapterName").click(function() {
        $("#EditChapterName").val();
        $("#HiddenEditChapterId").val();
        if ($("#EditChapterName").val().length !== 0) {
            var formdata = new FormData();
            formdata.append('id',$("#HiddenEditChapterId").val());
            formdata.append('chapter_name',$("#EditChapterName").val());
            $.ajax({
                processData:false,
                contentType:false,
                data:formdata,
                type:"post",
                url:"UpdateChapter",
                success:function(data) {
                    $("#EditChapterNameModal").modal('hide');
                    // alert(data);
                    show_chapter_list();
                }
            });
        }
        else{
            alert("Chapter field is empty");
        }
    });
    // update functions


    // topic update
    $("#UpdateTopic").click(function() {
        if ($("#EditTopicChapter").val().length !== 0 && $("#EditTopicName").val().length !== 0) {
            var formdata = new FormData();
            formdata.append('id',$("#HiddenEditTopicId").val());
            formdata.append('chapter_id',$("#EditTopicChapter").val());
            formdata.append('topic_name',$("#EditTopicName").val());
            $.ajax({
                processData:false,
                contentType:false,
                data:formdata,
                type:"post",
                url:"UpdateTopic",
                success:function(data) {
                    $("#EditTopicModal").modal('hide');
                    // alert(data);
                    show_topics();
                }
            });
        }
        else{
            alert("Chapter field is empty");
        }
    });


    // update functions

    // show functions// show chapter
    show_chapter_list();
    // show topic
    show_topics();
    // show board
    show_board();
    // show question
    Show_Question();

});  // jquery function end here

// view questions by modal
function view_question(id) {
    $.ajax({
        processData:false,
        contentType:false,
        type:"get",
        url:"ViewQuestion/"+id+"",
        success:function(data){
            all = JSON.parse(data);
            // $("#View_Chapter").val(all.topic.chapter.chapter_name);
            $("#View_id").html(all.id);
            $("#View_Topic").val(all.topic.topic_name);
            $("#View_Question").val(all.question);
            $("#View_Option1").val(all.option1);
            $("#View_Option2").val(all.option2);
            $("#View_Option3").val(all.option3);
            $("#View_Option4").val(all.option4);
            $("#View_Correct_Option").val(all.correct_option);
            $("#View_Detail").val(all.details);
            // if (typeof all.board_list.board_name === 'undefined') {
            //     alert('null');
            // }
            // else{
            //     alert('not null');
                // $("#View_Board").val(all.board_list.board_name+" "+all.board_list.year);
            // }
            $("#View_Tag").val(all.tag);
            $("#QuestionModal").modal('show');
        }
    })
}

// show question
function Show_Question(){
    $.ajax({
        processData:false,
        contentType:false,
        type:"get",
        url:"show_question",
        success:function(data){
              var a = JSON.parse(data);
            $("#Question_table").html(a.data);
            $("#total_question").html(a.total_question);
              $("#with_tag_question").html(a.with_tag_question);
            
        }
    })
}


// show board
function show_board() {
    $.ajax({
        processData:false,
        contentType:false,
        type:"get",
        url:"ShowBoardlist",
        success:function(data){
            all = JSON.parse(data);
            $("#BoardListDelete").html(all.delete);
            $("#question_board").html(all.board_list);
            $("#Edit_Board").html(all.board_list);
        }
    })
}


// show topic
function show_topics() {
    $.ajax({
        processData:false,
        contentType:false,
        type:"get",
        url:"ShowTopiclist",
        success:function(data){
            all = JSON.parse(data);
            $("#EditTopicList").html(all.edit);
            $("#DeleteTopicList").html(all.delete);
            $("#question_topic").html(all.topic_list);
            $("#Edit_Topic").html(all.topic_list);
        }
    })
}

// show chapter
function show_chapter_list() {
    $.ajax({
        processData:false,
        contentType:false,
        type:"get",
        url:"ShowChapterlist",
        success:function(data){
            all = JSON.parse(data);

            $("#EditChapterList").html(all.edit_chapter);
            $("#DeleteChapterList").html(all.delete_chapter);
            $("#Topic-chapter").html(all.chapter_list);
            $("#EditTopicChapter").html(all.chapter_list);
        }
    });
}

// delete board
function Delete_Board(id) {
    $.ajax({
        processData:false,
        contentType:false,
        type:"get",
        url:"DeleteBoard/"+id+"",
        success:function(data){
            // alert("Successfully deleted");
            show_board();
        }
    })
}

// delete chapter
function Delete_Chapter(id) {
    $.ajax({
        processData:false,
        contentType:false,
        type:"get",
        url:"DeleteChapter/"+id+"",
        success:function(data){
            // alert("Successfully deleted");
            show_chapter_list();
        }
    })
}

// delete topic
function Delete_Topic(id) {
    $.ajax({
        processData:false,
        contentType:false,
        type:"get",
        url:"DeleteTopic/"+id+"",
        success:function(){
            // alert("Successfully deleted");
            show_topics();
        }
    })
}


// delete question
function delete_question(id) {
    $.ajax({
        processData:false,
        contentType:false,
        type:"get",
        url:"DeleteQuestion/"+id+"",
        success:function(data){
            Show_Question();
        }
    })
}


// edit chapter name
function Edit_Chapter(id) {
    $.ajax({
        processData:false,
        contentType:false,
        type:"get",
        url:"EditChapterName/"+id+"",
        success:function(data){
            all = JSON.parse(data);
            $("#EditChapterName").val(all.chapter_name);
            $("#HiddenEditChapterId").val(all.id);
            $("#EditChapterNameModal").modal('show');
        }
    })
}


// edit topic
function Edit_Topic(id) {
    $.ajax({
        processData:false,
        contentType:false,
        type:"get",
        url:"EditTopic/"+id+"",
        success:function(data){
            all = JSON.parse(data);
            $("#EditTopicName").val(all.topic_name);
            $("#EditTopicChapter").val(all.chapter_id);
            $("#HiddenEditTopicId").val(all.id);
            $("#EditTopicModal").modal('show');
        }
    })
}


// edit question
function edit_question(id) {
    $.ajax({
        processData:false,
        contentType:false,
        type:"get",
        url:"EditQuestion/"+id+"",
        success:function(data){
            all = JSON.parse(data);
            // $("#Edit_Chapter").val(all.topic.chapter.chapter_name);
            $("#Edit_question_hidden_id").val(all.id);
            $("#Edit_Topic").val(all.topic_id);
            $("#Edit_Question").val(all.question);
            $("#Edit_Option1").val(all.option1);
            $("#Edit_Option2").val(all.option2);
            $("#Edit_Option3").val(all.option3);
            $("#Edit_Option4").val(all.option4);
            $("#Edit_Correct_Option").val(all.correct_answer);
            $("#Edit_Detail").val(all.details);
            $("#Edit_Board").val(all.board_id);
            $("#Edit_Tag").val(all.tag);
            $("#EditQuestionModal").modal('show');
        }
    })
}


















$(document).ajaxStart(function() {
    swal({
        //  title: "Loading...",
        //  text: "Please wait",
        icon: "assets/images/loading-gif-png-5.gif",
        button: false,
        closeOnClickOutside: false,
        closeOnEsc: false,
    });
    $(".swal-modal").css('background-color', 'rgba(0,0,0,0.0)');
    $(".swal-overlay").css('background-color', 'rgba(0,0,0,0.8)');
    $('.swal-icon').css({
        "height":"180px",
    });
});
$(document).ajaxStop(function() {
    swal.close();
});








