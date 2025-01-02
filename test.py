import mysql.connector
import firebase_admin
from firebase_admin import credentials, messaging

# Initialize the Firebase Admin SDK
cred_obj = credentials.Certificate('seaweedfyp-firebase-adminsdk-ky1sy-0eaca81589.json')
default_app = firebase_admin.initialize_app(cred_obj)


host = "localhost"  # or the IP address of your MySQL server
user = "seaweedadmin"
password = "SeaweedFarmingIot2024"
database = "seaweed_farming"
try:
    # Establish a connection
    connection = mysql.connector.connect(
        host=host,
        user=user,
        password=password,
        database=database
    )

    if connection.is_connected():
        print("Connected to MySQL database")

except mysql.connector.Error as e:
    print(f"Error connecting to MySQL: {e}")


def send_fcm_notification(fcm_token, title, body):
    """
    Sends an FCM notification to a specific token.
    
    :param fcm_token: The recipient's FCM token
    :param title: Notification title
    :param body: Notification body
    """
    try:
        # Create a message
        message = messaging.Message(
            notification=messaging.Notification(
                title=title,
                body=body,
            ),
            token=fcm_token,
        )
        
        # Send the message
        response = messaging.send(message)
        print(f"Successfully sent message: {response}")
    except Exception as e:
        print(f"Error sending message: {e}")
# Create a cursor object
cursor = connection.cursor(prepared=True)  # Enable prepared statements
#select_query = "SELECT * FROM users;"
#cursor.execute(select_query, ("value1",))
cursor.execute(select_query)
# Fetch and display results
result = cursor.fetchall()


queryselectall = """WITH RecentEntries AS (
    SELECT 
        fd.*
    FROM 
        farm_data fd
    WHERE 
        fd.time >= NOW() + INTERVAL 7 HOUR + INTERVAL 55 MINUTE
),
RankedEntries AS (
    SELECT 
        re.*,
        ROW_NUMBER() OVER (PARTITION BY re.farm_token ORDER BY re.time DESC) AS row_num
    FROM 
        RecentEntries re
),
LatestEntries AS (
    SELECT 
        re.data_id, 
        re.farm_token, 
        re.ph_value, 
        re.temperature, 
        re.salinity, 
        re.light_intensity, 
        re.time,
        f.user_id
    FROM 
        RankedEntries re
    JOIN 
        farms f
    ON 
        re.farm_token = f.farm_token
    WHERE 
        re.row_num = 1
)
SELECT 
    le.data_id, 
    le.farm_token, 
    le.ph_value, 
    le.temperature, 
    le.salinity, 
    le.light_intensity, 
    le.time,
    le.user_id,
    uft.fcm_token,
    uft.device_type
FROM 
    LatestEntries le
LEFT JOIN 
    user_fcm_tokens uft
ON 
    le.user_id = uft.user_id;"""


cursor.execute(queryselectall)
# Fetch and display results
result = cursor.fetchall()
print(result)