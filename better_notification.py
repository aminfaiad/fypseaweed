import mysql.connector
import firebase_admin
from firebase_admin import credentials, messaging
import time
import sys

# Log setup
log_file = open('tokenlog.log', 'a')
sys.stdout = log_file

# Firebase setup
FIREBASE_CREDENTIALS_FILE = 'seaweedfyp-firebase-adminsdk-ky1sy-0eaca81589.json'
cred_obj = credentials.Certificate(FIREBASE_CREDENTIALS_FILE)
firebase_admin.initialize_app(cred_obj)

# Database connection details
DB_CONFIG = {
    "host": "localhost",
    "user": "seaweedadmin",
    "password": "SeaweedFarmingIot2024",
    "database": "seaweed_farming",
}

# Notification threshold values
THRESHOLDS = {
    "ph": {"min": 8, "max": 9},
    "temperature": {"min": 28, "max": 34},
    "salinity": {"min": 28, "max": 35},
}

# SQL query to fetch the latest environmental data
QUERY_SELECT_ALL = """
WITH RecentEntries AS (
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
    le.user_id = uft.user_id;
"""

# Connect to the database
def connect_to_database():
    try:
        connection = mysql.connector.connect(**DB_CONFIG)
        if connection.is_connected():
            print("Connected to MySQL database")
        return connection
    except mysql.connector.Error as e:
        print(f"Error connecting to MySQL: {e}")
        return None

# Send FCM notification
def send_fcm_notification(fcm_token, title, body):
    try:
        message = messaging.Message(
            notification=messaging.Notification(
                title=title,
                body=body,
            ),
            token=fcm_token,
        )
        response = messaging.send(message)
        print(f"Successfully sent message: {response}")
    except Exception as e:
        print(f"Error sending message: {e}")

# Check environmental conditions
def check_conditions_and_notify(connection):
    cursor = connection.cursor(prepared=True)
    cursor.execute(QUERY_SELECT_ALL)
    results = cursor.fetchall()
    cursor.close()

    # Column index mapping
    column_names = [desc[0] for desc in cursor.description]
    idx_ph = column_names.index("ph_value")
    idx_temp = column_names.index("temperature")
    idx_salinity = column_names.index("salinity")
    idx_fcm = column_names.index("fcm_token")

    for row in results:
        ph = row[idx_ph]
        temp = row[idx_temp]
        salinity = row[idx_salinity]
        fcm_token = row[idx_fcm]

        # Check thresholds
        if (
            ph < THRESHOLDS["ph"]["min"] or ph > THRESHOLDS["ph"]["max"] or
            temp < THRESHOLDS["temperature"]["min"] or temp > THRESHOLDS["temperature"]["max"] or
            salinity < THRESHOLDS["salinity"]["min"] or salinity > THRESHOLDS["salinity"]["max"]
        ):
            send_fcm_notification(
                fcm_token,
                "WARNING",
                "Your seaweed farm needs urgent care"
            )

# Main loop
def main():
    connection = connect_to_database()
    if not connection:
        return

    try:
        while True:
            connection.reconnect()
            check_conditions_and_notify(connection)
            time.sleep(60)
    except KeyboardInterrupt:
        print("Stopping...")
    finally:
        if connection.is_connected():
            connection.close()
            print("Database connection closed")

if __name__ == "__main__":
    main()
