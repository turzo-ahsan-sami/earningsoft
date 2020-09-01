<script type="text/javascript">

$(document).ready(function(){

    var companyId = {{ $companyId }}

    $.ajax({

      type:'get',
      url:'{!! URL::to('/ajaxBranch') !!}',
      data:{'companyId':companyId},
      success:function(data){

          $('#filBranch').empty();

          $.each(data, function(index, val) {

            $("#filBranch").append("<option value='"+val.id+"'>"+val.nameWithCode+"</option>");

          });
      },

      error:function(){
        alert('Something is wrong....!');
      }
    });
  });

</script>
