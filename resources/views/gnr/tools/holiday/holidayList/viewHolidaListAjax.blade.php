<script type="text/javascript">
$(document).ready(function(){
var member;
var SSname;
  $(document).on('change','#searchCompany',function(){
    // var product_id = $("#searchBranch").val();
    var product_id=$(this).val();
    console.log('Inside');
    console.log(product_id);
    // console.log('from jQuery');
    var dropdown = $('#searchBranch');
    dropdown.empty();

    var divt= $(this).parent();
    var op =' ';

    $.ajax({
      type:'POST',
      url:'{!! URL::to('./gnr/viewHolidayListAjaxBranch/') !!}',
      data:{'id':product_id},
      success:function(data){
        console.log('success');
        console.log(data.length);
        var a = 0;
        if (data.length>0) {
          for (var i = 0; i < data.length; i++) {
            console.log(data[i].branchId);
            console.log('outside');
            console.log('Branch ID : '+data[i].id);
            console.log('Branch Name : ', data[i].name);

            if (a == 0) {
                op+='<option value="" style="color: black;">-- select --</option>';
                a = a + 1;
              }
            op+='<option value="'+data[i].id+'" style="color: black;">'+data[i].branchCode +' - '+ data[i].name+'</option>';

          }
        }
        else {
          op+='<option value="" disabled style="color: black;">NO DATA FOUND !</option>';
        }

        // op+='<option value="'+data[i].status+'" style="color: black;">'+data[i].status+'- Acti</option>';

        divt.find('#searchBranch').html(' ');
        $('#searchBranch').append(op);
      },
      error:function(){
        alert('Something is wrong....!');
      }
    });
  });

// END OF SCRIPT

});



$(document).ready(function(){
var member;
var SSname;
  $(document).on('change','#searchBranch',function(){
    // var product_id = $("#searchBranch").val();
    var product_id=$(this).val();
    console.log('Inside');
    console.log(product_id);
    // console.log('from jQuery');
    var dropdown = $('#searchSamity');
    dropdown.empty();

    var divt= $(this).parent();
    var op =' ';

    $.ajax({
      type:'POST',
      url:'{!! URL::to('./gnr/viewHolidayListAjaxSamity/') !!}',
      data:{'id':product_id},
      success:function(data){
        console.log('success');
        console.log(data.length);
        var a = 0;
        if (data.length>0) {
          for (var i = 0; i < data.length; i++) {
            console.log(data[i].branchId);
            console.log('outside');
            console.log('Branch ID : '+data[i].id);
            console.log('Branch Name : ', data[i].name);

            if (a == 0) {
                op+='<option value="" style="color: black;">-- select --</option>';
                a = a + 1;
              }
            op+='<option value="'+data[i].id+'" style="color: black;">'+data[i].code +' - '+ data[i].name+'</option>';

          }
        }
        else {
          op+='<option value="" disabled style="color: black;">NO DATA FOUND !</option>';
        }

        // op+='<option value="'+data[i].status+'" style="color: black;">'+data[i].status+'- Acti</option>';

        divt.find('#searchSamity').html(' ');
        $('#searchSamity').append(op);
      },
      error:function(){
        alert('Something is wrong....!');
      }
    });
  });

// END OF SCRIPT

});

</script>
