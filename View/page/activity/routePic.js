function fileSelect(id){
    document.getElementById("routeid").value = id;
    document.getElementById("uploadfile").click();
}

function fileSelected() {
    var fileName = document.getElementById("uploadfile").value;
    if(fileName=="")
    {
        return ;
    }
    document.getElementById("uploadForm").submit();
}

callback = function(resp,respMsg,src) {
    if(resp=="1"){
        //$("#"+selectedId).attr("src",server_url+path+no+"/"+str);
    }
    else{
        alert(respMsg);
    }
}