<!DOCTYPE html>
<html>
<body>
    <form>
        Keyword:<br>
        <input type="text" name="keyword" onkeyup="callREST()"><br>
    </form>
    <div id="response"></div>

    <script>
        function callREST() {
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("response").innerHTML = this.responseText;
                }
            };
            xhttp.open("GET", "URL", true);
            xhttp.send();
        }
    </script>
</body>
</html>