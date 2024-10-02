<!DOCTYPE html>
<html lang="en">
    <head>
        <title> Aplikasi Kriptografi Playfair </title>
        <meta charset="utf-8">
        <meta rel="viewport" href="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    </head>
    <body>

        <div class="jumbotron jumbotron-fluid" style="background-color: cornflowerblue;">
            <div class="container">
                <h1 style="color: white;">Aplikasi Kriptografi Playfair  KELOMPOK 9</h1>
            </div>
        </div>
        
        <div class="container">
            <div class="row">
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    <form action="/action_Page.php">

                    
                        <div class="form-group">
                            <label for="pesan"><h5>Masukkan Pesan</h5></label>
                            <textarea id="pesan" name="pesan" class="form-control"></textarea>
                            <small class="text-muted">Masukan yang di perbolehkan: huruf</small>
                            <input type="file" id="fileInput" class="form-control-file" accept=".txt" onchange="loadFile(event)">
                            <small class="text-muted">File yang diizinkan: .txt</small>
                        </div>

                        <div class="form-group">
                            <label for="cipherMode"><h5>Pilih Metode Kriptografi</h5></label>
                            <select id="cipherMode" class="form-control" onchange="toggleCipherOptions()">
                                <option value="playfair">Playfair Cipher</option>
                                <option value="vigenere">Vigenere Cipher</option>
                            </select>
                        </div>


                        <div class="form-group">
                            <label for="katakunci"><h5>Kunci</h5></label>
                            <input type="number" min="0" max="25" id="kunci" name="kunci" class="form-control col-md-6">
                            <small class="text-muted">Masukkan yang di perbolehkan: angka(1-26)</small>
                        </div>
                        <div class="form-group" id="enkripsi" style="display: none;">
                            <label for="pesan"><h5>Hasil Enkripsi</h5></label>
                            <textarea id="hasil_enkripsi" name="hasil_enkripsi" class="form-control"></textarea>
                        </div>
                        <div class="form-group" id="dekripsi" style="display: none;">
                            <label for="pesan"><h5>Hasil Dekripsi</h5></label>
                            <textarea id="hasil_dekripsi" name="hasil_dekripsi" class="form-control"></textarea>
                        </div>
                        <br>
                        <div>
                            <button type="button" class="btn btn-secondary" id="btn_enkripsi" name="btn_enkripsi" onclick="enkripsi()">Enkripsi</button>
                            <button type="button" class="btn btn-secondary" id="btn_dekripsi" name="btn_dekripsi" onclick="dekripsi()">Dekripsi</button>
                            <button type="reset" class="btn btn-danger" id="btn_batal" name="btn_batal">Batal</button>
                            <button type="button" class="btn btn-primary" id="btn_download_enkripsi" style="display: none;" onclick="downloadFile('hasil_enkripsi', 'Hasil_Enkripsi.txt')">Download Enkripsi</button>
                            <button type="button" class="btn btn-primary" id="btn_download_dekripsi" style="display: none;" onclick="downloadFile('hasil_dekripsi', 'Hasil_Dekripsi.txt')">Download Dekripsi</button>
                            <a href=""><button type="button" class="btn btn-danger" id="btn_reset" name="btn_reset" onclick="reset()" style="display: none;">Reset</button></a>
                            <a href=""><button type="button" class="btn btn-danger" id="btn_reset" name="btn_reset" onclick="reset()" style="display: none;">hasil_dekripsi</button></a>
                        </div>
                    </form>
                </div>
                </div>
        </div>

        <br><br>
        <hr>
        <div class="container">
            <p> Ahmad Nur Ramadhani - 2111102441161 </p>
            <p> M. Ervan Romadon - 2111102441123 </p>
            <p> Riya Nur Fadillah - 2111102441078  </p>
        </div>

        <script type="text/javascript">

        // Fungsi untuk membaca file dan memasukkan isinya ke dalam textarea
        document.getElementById('fileInput').addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('pesan').value = e.target.result; // Masukkan konten file ke textarea
                }
                reader.readAsText(file);
            }
        });

        function generateMatrix(key) {

            
            key = key.toUpperCase().replace(/J/g, 'I'); // Ganti 'J' dengan 'I'
            var matrix = [];
            var seen = new Set();

            // Tambahkan huruf unik dari kunci
            for (var char of key) {
                if (char >= 'A' && char <= 'Z' && !seen.has(char)) {
                    seen.add(char);
                    matrix.push(char);
                }
            }

            // Tambahkan huruf yang tersisa dari alfabet
            for (var char = 'A'; char <= 'Z'; char = String.fromCharCode(char.charCodeAt(0) + 1)) {
                if (char === 'J') continue; // Lewati 'J'
                if (!seen.has(char)) {
                    seen.add(char);
                    matrix.push(char);
                }
            }

            return matrix;
        }

        function toggleCipherOptions() {
            var selectedCipher = document.getElementById("cipherMode").value;
    
            // Hide all cipher options initially
            document.getElementById("playfairOptions").style.display = "none";
            document.getElementById("vigenereOptions").style.display = "none";

            // Show the relevant options based on the selected cipher
            if (selectedCipher === "playfair") {
                document.getElementById("playfairOptions").style.display = "block";
            } else if (selectedCipher === "vigenere") {
                document.getElementById("vigenereOptions").style.display = "block";
            }
        }

        function prepareText(text) {
            text = text.toUpperCase().replace(/J/g, 'I').replace(/[^A-Z]/g, ''); // Hanya ambil huruf
            var pairs = [];
            for (var i = 0; i < text.length; i += 2) {
                var a = text[i];
                var b = text[i + 1];
                if (b === undefined || a === b) {
                    pairs.push(a + 'X'); // Tambahkan 'X' jika huruf sama atau jika pasangan tidak lengkap
                } else {
                    pairs.push(a + b);
                }
            }
            return pairs;
        }

        function findPosition(char, matrix) {
            var index = matrix.indexOf(char);
            return [Math.floor(index / 5), index % 5]; // Kembalikan baris dan kolom
        }

        function encryptPair(pair, matrix) {
            var [row1, col1] = findPosition(pair[0], matrix);
            var [row2, col2] = findPosition(pair[1], matrix);

            if (row1 === row2) {
                // Baris yang sama: Geser ke kanan
                return matrix[row1 * 5 + (col1 + 1) % 5] + matrix[row2 * 5 + (col2 + 1) % 5];
            } else if (col1 === col2) {
                // Kolom yang sama: Geser ke bawah
                return matrix[((row1 + 1) % 5) * 5 + col1] + matrix[((row2 + 1) % 5) * 5 + col2];
            } else {
                // Swap persegi panjang
                return matrix[row1 * 5 + col2] + matrix[row2 * 5 + col1];
            }
        }

        function decryptPair(pair, matrix) {
            var [row1, col1] = findPosition(pair[0], matrix);
            var [row2, col2] = findPosition(pair[1], matrix);

            if (row1 === row2) {
                // Baris yang sama: Geser ke kiri
                return matrix[row1 * 5 + (col1 + 4) % 5] + matrix[row2 * 5 + (col2 + 4) % 5];
            } else if (col1 === col2) {
                // Kolom yang sama: Geser ke atas
                return matrix[((row1 + 4) % 5) * 5 + col1] + matrix[((row2 + 4) % 5) * 5 + col2];
            } else {
                // Swap persegi panjang
                return matrix[row1 * 5 + col2] + matrix[row2 * 5 + col1];
            }
        }

            // Function to encrypt using the Playfair cipher
            function enkripsi() {
            var text = document.getElementById("pesan").value;
            var key = document.getElementById("kunci").value;

            var matrix = generateMatrix(key);
            var pairs = prepareText(text);
            var result = '';

            for (var pair of pairs) {
                result += encryptPair(pair, matrix);
            }

                document.getElementById("enkripsi").style.display = "block";
                document.getElementById("hasil_enkripsi").value = result;
                document.getElementById("btn_enkripsi").style.display = "none";
                document.getElementById("btn_dekripsi").style.display = "none";
                document.getElementById("btn_batal").style.display = "none";
                document.getElementById("btn_reset").style.display = "block";
                document.getElementById("btn_download_enkripsi").style.display = "inline-block"; // Show download button
                toggleButtons(false);
            }

            // Function to decrypt using the Playfair cipher
            function dekripsi() {
            var text = document.getElementById("pesan").value;
            var key = document.getElementById("kunci").value;

            var matrix = generateMatrix(key);
            var pairs = prepareText(text);
            var result = '';

            for (var pair of pairs) {
                result += decryptPair(pair, matrix);
            }

                document.getElementById("dekripsi").style.display = "block";
                document.getElementById("hasil_dekripsi").value = result;
                document.getElementById("btn_enkripsi").style.display = "none";
                document.getElementById("btn_dekripsi").style.display = "none";
                document.getElementById("btn_batal").style.display = "none";
                document.getElementById("btn_reset").style.display = "block";
                document.getElementById("btn_download_dekripsi").style.display = "block"; // Show download button
                toggleButtons(false);
            }

            function downloadFile(resultId, filename) {
            var text = document.getElementById(resultId).value;
            var blob = new Blob([text], { type: 'text/plain' });
            var link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = filename;
            link.click();
            URL.revokeObjectURL(link.href); // Clean up the URL object
            }
            function toggleButtons(show) {
                document.getElementById("btn_enkripsi").style.display = show? "block" : "none";
                document.getElementById("btn_dekripsi").style.display = show? "block" : "none";
            }
        </script>

            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min,js"></script>
                <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        </body>
        </html>