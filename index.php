<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student CRUD Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
        }
        .student-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 10px;
            background-color: #f9f9f9;
        }
        .student-item div {
            flex: 1;
            padding: 0 10px;
        }
        .student-item div.username {
            flex: 2;
        }
        .student-item div.email {
            flex: 3;
        }
        .student-item div.actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="mt-4 p-5 mb-5 bg-primary text-white rounded">
            <h1>Student CRUD Application Using Axios</h1>
        </div>

        <!-- Add Student Form -->
        <h2 class="mt-5 mb-3">Add Student</h2>
        <form id="createStudentForm">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" id="studentUsername" name="username" placeholder="Username" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <input type="email" id="studentEmail" name="email" placeholder="Email" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <input type="password" id="studentPassword" name="password" placeholder="Password" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">Add Student</button>
                </div>
            </div>
        </form>

        <!-- Student List -->
        <h2 class="mt-5">Student List</h2>
        <div id="studentsList">
            <!-- Students will be loaded here dynamically -->
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => { // jab HTML page poori tarah load ho jata hai, tab yeh function kaam karta hai.
            function loadStudents() {
                axios.get('read_students.php')//server se data hasil kar rahe hain. Yeh request server se information mangti hai, lekin koi data send nahi karti.
                    .then(response => { // GET request successful hoti hai, to server se jo data milta hai,
                        const students = response.data; //response.data ka istemal karte hain taake aap sirf us data ko le sakein jo aapko chahiye.
                        let studentsList = '';
                        students.forEach(student => { //student => ka matlab hai ke yeh function har student ka data le raha hai taake aap us par operations kar sakein.
                            studentsList += `
                                <div class="student-item" id="student-${student.id}">
                                    <div class="username">${student.username}</div>
                                    <div class="email">${student.email}</div>
                                    <div class="actions">
                                        <button onclick="editStudent(${student.id}, '${student.username}', '${student.email}')">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deleteStudent(${student.id})">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            `;
                        });
                        document.getElementById('studentsList').innerHTML = studentsList;
                    })
                    .catch(error => console.error('Error fetching students:', error));
            }

            loadStudents(); // Load students when the page is loaded

            document.getElementById('createStudentForm').addEventListener('submit', function (e) {
                e.preventDefault(); //Browser ka default action rokna.Jab form submit karte hain, toh normally page reload hota hai.

                const username = document.getElementById('studentUsername').value;
                const email = document.getElementById('studentEmail').value;
                const password = document.getElementById('studentPassword').value;

                axios.post('create_student.php', new URLSearchParams({ // POST request bhej rahe hain. Yeh request data bhejne ke liye hoti hai.
                    // new URLSearchParams URL encoded form mein convert karta hai.
                    username: username,
                    email: email,
                    password: password
                }), {
                    headers: {
                        //yeh header server ko yeh batata hai ke aap jo data bhej rahe hain,
                        // wo form ke through encode kiya gaya hai, jisse server usay sahi tareeqe se samajh sake.
                        'Content-Type': 'application/x-www-form-urlencoded'
                    }
                })
                .then(response => {
                    const result = response.data;
                    if (result.success) {
                        loadStudents(); // Reload students list on success
                        document.getElementById('createStudentForm').reset(); // Reset the form fields
                        Swal.fire({
                            title: 'Success!',
                            text: 'Student added successfully.',
                            icon: 'success'
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: result.message,
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error adding student:', error);
                    Swal.fire({
                        title: 'Error!',
                        text: 'An error occurred. Please check the console for details.',
                        icon: 'error'
                    });
                });
            });

            window.editStudent = function(id, currentUsername, currentEmail) {
                const newUsername = prompt('New username:', currentUsername);
                const newEmail = prompt('New email:', currentEmail);
                const newPassword = prompt('New password:', '');

                if (newUsername && newEmail && newPassword) {
                    axios.post('update_student.php', new URLSearchParams({
                        id: id,
                        username: newUsername,
                        email: newEmail,
                        password: newPassword
                    }))
                    .then(response => {
                        if (response.data.success) {
                            loadStudents(); // Reload students list on success
                            Swal.fire({
                                title: 'Updated!',
                                text: 'Student information has been updated.',
                                icon: 'success'
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.data.message,
                                icon: 'error'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error updating student:', error);
                        Swal.fire({
                            title: 'Error!',
                            text: 'An error occurred while updating. Please try again.',
                            icon: 'error'
                        });
                    });
                }
            }

            window.deleteStudent = function(id) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: 'You won\'t be able to revert this!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.post('delete_student.php', new URLSearchParams({ id: id }))
                        .then(response => {
                            if (response.data.success) {
                                document.getElementById('student-' + id).remove(); // Remove the student from the list on success
                                Swal.fire({
                                    title: 'Deleted!',
                                    text: 'Student has been deleted.',
                                    icon: 'success'
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error!',
                                    text: response.data.message,
                                    icon: 'error'
                                });
                            }
                        })
                        .catch(error => console.error('Error deleting student:', error));
                    }
                });
            }
        });
    </script>
</body>
</html>
