<?php 
session_start();
include_once "config.php";
$fname = mysqli_real_escape_string($conn, $_POST['fname']);
$lname = mysqli_real_escape_string($conn, $_POST['lname']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = mysqli_real_escape_string($conn, $_POST['password']);


if(!empty($fname) && !empty($lname) && !empty($email) && !empty($password)){
    //let's check user email i valid or not
    if(filter_var($email, FILTER_VALIDATE_EMAIL)){  //if email is valid
        //let's check that email already exists in the database or not
        $sql = mysqli_query($conn, "SELECT email FROM users WHERE email = '{$email}'");
        if(mysqli_num_rows($sql) > 0){   //if email is already exists
            echo "$email -This email already exists!!"; 
        }else{
            //let's check user upload file or not
            if(isset($_FILES['image'])){ //if file is uploaded
                $img_name = $_FILES['image']['name']; //getting user uploaded img name
                $img_type = $_FILES['image']['type']; //getting user uploaded img type
                $tmp_name = $_FILES['image']['tmp_name']; //this temporary name is used to save/move file in our folder

                //let's explode image and get the last extension like jpg png of an user uploaded img file
                $img_explode = explode('.', $img_name);
                $img_ext = end($img_explode);  //there we get the extension of an user uploaded img file

                $extensions = ['png', 'jpeg', 'jpg']; //these are some valid img ext and we've store them in array
                if(in_array($img_ext, $extensions) === true){  //if user uploaded img ext is matched with any array extensions
                    $time = time(); //this will return us current time..
                                    //we need this time because when you uploading user img to in our folder we rename user file with curent time 
                                    //so all the image file will have a unique name
                                    //let's move the user uploaded img to our particular folder

                                    $new_img_name = $time.$img_name;


                                    if(move_uploaded_file($tmp_name, "images/".$new_img_name)){  //if user upload img move to our folder successfully
                                        $status = "Active now"; //once user signed up then his status will be active now
                                        $random_id = rand(time(), 1000000); //creating random id for user

                                        //let's insert all user in user data inside table
                                        $sq12 = mysqli_query($conn, "INSERT INTO users (unique_id, fname, lname, email, password, img, status)
                                        VALUES ({$random_id}, '{$fname}', '{$lname}', '{$email}', '{$password}', '{$new_img_name}', '{$status}')");

                                        if($sq12){ //if these data inserted
                                            $sq13 = mysqli_query($conn, "SELECT * FROM users WHERE email = '{$email}'");
                                            if(mysqli_num_rows($sq13) > 0){
                                                $row = mysqli_fetch_assoc($sq13);
                                                $_SESSION['unique_id'] = $row['unique_id']; //using this session we used user unique_id in other php files
                                                echo "success";

                                            }

                                        }else{
                                            echo "something went wrong!";
                                        }

                                    }
                                  
                }else{
                    echo "Please select an Image file - jpeg, jpg, png !";

                }
                 

        

            }else{
                echo "Please select an Image file!";
            }
        }

    }else{
        echo "$email - This is not a valid email";
    }

}else{
    echo "All the input fields are required!";
}
?>