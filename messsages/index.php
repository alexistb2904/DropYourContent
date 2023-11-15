<?php
require_once '../src/functions.php';

if ($_GET['user_to']) {
    $user_to = $_GET['user_to'];
} else {
    $user_to = null;
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Chat Application</title>
</head>

<body>
    <?php if ($user_to) { ?>
        <div id="chat-box">
            <ul id="messages">Chargement en cours...</ul>
            <input id="message-input" autocomplete="off" /><button id="send">Send</button>
        </div>

        <script>
            const user_id = "<?php echo "pedro"; ?>";
            const user_to = "<?php echo $user_to ?>";
            const socket = new WebSocket("ws://localhost:5620");

            socket.addEventListener("open", (event) => {
                console.log("Connected to the server websocket");
                const messages = document.getElementById("messages");
                messages.innerHTML = "";
                socket.send("{{%@" + user_id + "@%}} {{@%" + user_to + "%@}}");
            });

            socket.addEventListener("message", (event) => {
                console.log("Received message: " + event.data);
                const messages = document.getElementById("messages");
                const messageData = JSON.parse(event.data);
                const li = document.createElement("li");
                li.innerHTML = `Client: ${messageData.sender} | Message: ${messageData.context} | Timestamp: ${messageData.timestamp}`;
                messages.appendChild(li);
            });


            socket.addEventListener("close", (event) => {
                console.log("Connection closed");
            });

            document.getElementById("send").addEventListener("click", () => {
                const messageInput = document.getElementById("message-input");
                const message = messageInput.value + "{#%" + user_id + "%#} " + " {%#" + user_to + "#%}";
                socket.send(message);
                messageInput.value = "";
            });
        </script>
    <?php } else { ?>
        <h1>Aucun Utilisateur Renseigné</h1>
    <?php } ?>
</body>

</html>