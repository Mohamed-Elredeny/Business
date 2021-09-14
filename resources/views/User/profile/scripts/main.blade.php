<script>




    /*
    * Show Musics
    * My Musics
    * All Musics
    */
    function  showMusics(){
        document.getElementById('ez-body__center-content-profile').style.display = 'none';
        document.getElementById('ez-body__center-content-music').style.display = 'block';
    }
    function  showAllMusics(){
        document.getElementById('my-musics').style.display = 'none';
        document.getElementById('all-musics').style.display = 'block';
    }
    function  showMyMusics(){
        document.getElementById('my-musics').style.display = 'block';
        document.getElementById('all-musics').style.display = 'none';
    }

    function myFunction() {
        var input = document.getElementById("search");
        var filter = input.value.toLowerCase();
        var nodes = document.getElementsByClassName('target');

        for (i = 0; i < nodes.length; i++) {
            if (nodes[i].innerText.toLowerCase().includes(filter)) {
                nodes[i].style.display = "block";
            } else {
                nodes[i].style.display = "none";
            }
        }
    }

</script>
