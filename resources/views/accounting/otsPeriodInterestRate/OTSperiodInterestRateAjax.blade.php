<script type="text/javascript">
$(document).ready(function(){
var member;
var SSname;
  $(document).on('change','#periodName',function(){
    // var product_id = $("#searchBranch").val();
    var product_id=$(this).val();
    console.log('Inside');
    console.log(product_id);
    // console.log('from jQuery');
    var dropdown = $('#txtDate1');
    // dropdown.val('');
    dropdown.empty();

    var divt= $(this).parent();
    var op =' ';

    $.ajax({
      type:'get',
      url:'{!! URL::to('OTSperiodInterestHistoryAjax') !!}',
      data:{'id':product_id},
      success:function(data){
        console.log('success');
        console.log(data);
        console.log(product_id);
        var a = 0;
        // for (var i = 0; i < data.length; i++) {
        //   console.log(data[i]);
        //   // console.log('outside');
        //   // console.log('branch ID'+data[i].branchId);
        //   // console.log('Status', data[i].status);
        //
        //   if (a == 0) {
        //       // op+='<option value="" style="color: black;">-- select --</option>';
        //       // op+='<option value="all" style="color: black;">All</option>';
        //       // a = a + 1;
        //     }
        //   op+='<option value="" style="color: black;">'+data[i].openingDate+'</option>';
        //
        // }
        // op=data[0];
        // var dropdown = $('#txtDate1');
        // dropdown.val('');
        // $('#txtDate1').val('');
        $('#txtDate1').datepicker('destroy');

        $(function(){
          $( "#txtDate1" ).datepicker({
           dateFormat: "dd-mm-yy",
           showOtherMonths: true,
           selectOtherMonths: true,
           changeMonth: true,
           changeYear: true,
           yearRange: "-50:+0",
           minDate: new Date(data),
           maxDate: "dateToday"
           // setDate: new Date(data)
          }).val();
        })

        // op+='<option value="'+data[i].status+'" style="color: black;">'+data[i].status+'- Acti</option>';

        // divt.find('#txtDate1').html(' ');
        // $('#txtDate1').append(op);
      },
      error:function(){
        alert('Something is wrong....!');
      }
    });
  });

// END OF SCRIPT

});

</script>
