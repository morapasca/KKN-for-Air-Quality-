<!-- include php -->
<?php
include 'koneksi.php';
include 'header.php';
require_once __DIR__ . '/../vendor/autoload.php';
use Phpml\Classification\KNearestNeighbors;
use Phpml\Dataset\ArrayDataset;
use Phpml\Dataset\CsvDataset;
use Phpml\Metric\Accuracy;
use Phpml\Metric\ConfusionMatrix;
use Phpml\CrossValidation\StratifiedRandomSplit;
?>
<!-- akhir include php -->

    <!-- Start Breadcrumbs -->
    <div class="hitungdata breadcrumbs">
        <div class="container">            
            <div class="row align-items-center">
                <div class="col-12">
                    <div class="breadcrumbs-content">
                        <h3 class="page-title">Hitung Data</h3>
                    </div>
                </div>
                
                <div class="col-lg-6 col-md-12">
               
                <form action="" method="post" enctype="multipart/form-data">
                    
                    <div class="mb-3">
                        <label for="pm10" class="form-label">ISPU PM&#8321;&#8320;</label>
                        <input type="number" class="form-control" id="pm10" placeholder="Masukkan ISPU PM&#8321;&#8320;" min="1" name="parameter1" autocomplete="off" required>
                    </div>    
                    <div class="mb-3">
                        <label for="so2" class="form-label">ISPU SO&#8322;</label>
                        <input type="number" class="form-control" id="so2" placeholder="Masukkan ISPU SO&#8322;" min="1" name="parameter2" autocomplete="off" required>
                    </div>   
                    <div class="mb-3">
                        <label for="co" class="form-label">ISPU CO</label>
                        <input type="number" class="form-control" id="co" placeholder="Masukkan ISPU CO" min="1" name="parameter3" autocomplete="off" required>
                    </div> 
                    <div class="mb-3">
                        <label for="o3" class="form-label">ISPU O&#8323;</label>
                        <input type="number" class="form-control" id="o3" placeholder="Masukkan ISPU O&#8323;" min="1" name="parameter4" autocomplete="off" required>
                    </div> 
                    <div class="mb-3">
                        <label for="no2" class="form-label">ISPU NO&#8322;</label>
                        <input type="number" class="form-control" id="no2" placeholder="Masukkan ISPU NO&#8322;" min="1" name="parameter5" autocomplete="off" required>
                    </div>               
                    <hr>
                    <a href="hitungdata.php" class="btn btn-warning">Hitung Ulang</a>
                    <button type="submit" class="btn btn-primary" name="submit">Hitung</button>
                </form>
                </div>
                <div class="col-lg-6 col-md-12">
                <?php  
                if(isset($_POST['submit'])){   
                    $dataset = new CsvDataset(dirname(__FILE__). '/assets/dataset/FIX QC header mentah 2010-2020.csv', 5, true); //(file, jml atribut, header?)

                    $row = 1;

                    $samples = $dataset->getSamples();
                    $labels = $dataset->getTargets();

                    $pm10			    = $_POST['parameter1'];
                    $so2	            = $_POST['parameter2'];
                    $co		            = $_POST['parameter3'];
                    $o3		            = $_POST['parameter4'];
                    $no2		        = $_POST['parameter5'];
                    
                    $dtesting = array($pm10, $so2, $co, $o3, $no2);
                    
                    $class_hasil = "unknown";

                        $classifier = new KNearestNeighbors($k=5);
                        //train every labels
                        $classifier->train($samples, $labels); 
                        $class_hasil = $classifier->predict($dtesting);
                            
                        echo "<div class='col-12 text-center text-white'><h4 class='my-5'>Kategori Kualitas Udara adalah $class_hasil</h4></div>";

                    $sql = mysqli_query($koneksi, "INSERT INTO hasilhitung (pm10,so2,co,o3,no2,kategori) VALUES($pm10, $so2, $co, $o3, $no2, '$class_hasil')") or die(mysqli_error($koneksi));
                        
                }
                ?>
                </div>
                
            </div>
        </div>
    </div>
    <!-- End Breadcrumbs -->

    <div class="hasilhitung breadcrumbs">
        <div class="container wow fadeInUp" data-wow-delay=".2s">
            <h4 class="mb-3">Riwayat Hitung</h4>
            <table class="table table-hover table-responsive display responsive nowrap" style="width:100%" id="riwayathitung" data-wow-delay=".2s">
            <thead>
                <tr class="text-center">
                    <th >No.</th>
                    <th>Tanggal</th>
                    <th>PM&#8321;&#8320;</th>
                    <th>SO&#8322;</th>
                    <th>CO</th>
                    <th>O&#8323;</th>
                    <th>NO&#8322;</th>
                    <th>Kategori</th> 
                    <th>Aksi</th> 
                </tr>
            </thead>
            <tbody>
            <?php
            //query ke database SELECT tabel mahasiswa urut berdasarkan id yang paling besar
            $sql = mysqli_query($koneksi, "SELECT * FROM hasilhitung ORDER BY id_hasilhitung DESC") or die(mysqli_error($koneksi));
            //jika query diatas menghasilkan nilai > 0 maka menjalankan script di bawah if...
            if(mysqli_num_rows($sql) > 0){
                //membuat variabel $no untuk menyimpan nomor urut
                $no = 1;
                //melakukan perulangan while dengan dari dari query $sql
                while($data = mysqli_fetch_assoc($sql)){
                    //menampilkan data perulangan
                    echo '
                    <tr>
                        <td>'.$no.'</td>
                        <td>'.$data['tanggal'].'</td>
                        <td>'.$data['pm10'].'</td>
                        <td>'.$data['so2'].'</td>
                        <td>'.$data['co'].'</td>
                        <td>'.$data['o3'].'</td>
                        <td>'.$data['no2'].'</td>
                        <td>'.$data['kategori'].'</td>
                        <td>
                            <a href="hitungdata_delete.php?id_hasilhitung='.$data['id_hasilhitung'].'" class="btn btn-danger btn-sm" onclick="return confirm(\'Yakin ingin menghapus data ini?\')">Delete</a>
                        </td>
                    </tr>
                    ';
                    $no++;
                }
            }
            ?>
            </tbody>
            </table>
        </div>
    </div>

<!-- footer -->
<?php include 'footer.php'; ?>
<!-- akhir footer -->
<script type="text/javascript">
    $(document).ready( function () {
    $('#riwayathitung').DataTable();
    } );
</script>
</body>

</html>