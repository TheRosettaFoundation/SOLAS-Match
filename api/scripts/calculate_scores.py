#!/usr/bin/python

import MySQLdb as mdb
import sys, os
import ConfigParser
import string
import time
import math

con = None
settings = dict()

#
# Load the configuration file that stores the Database info
#
def LoadConfig():
    path = os.path.abspath(sys.argv[0])     # get the path to the currently executing script
    dir_file = os.path.split(path)          # Split up the file name and the path
    file_name = "/../includes/conf.ini"     # Relative location
    conf_file = dir_file[0] + file_name
    parser = ConfigParser.ConfigParser()
    parser.read(conf_file)
    for section in parser.sections():
        name = string.lower(section)
        for opt in parser.options(section):
            settings[name + "." + string.lower(opt)] = string.strip(parser.get(section, opt)).replace("\"", '').replace("\'", '')

def DBCall(proc_name, args = ()):
    query = "call %s(" % proc_name

    if len(args) > 1:
        i = 0
        while i < len(args):
            query += str(args[i])
            if i < len(args) - 1:
                query += ", "
            i += 1
    else:
        query = query + str(args[0])
    query = query + ")"
    con = None
    try:
        rows = None
        #Must specify read_default_group so that it will read my.cnf for the socket
        con = mdb.connect(host = settings['database.server'],
               user = settings['database.username'],
               passwd = settings['database.password'],
               db = settings['database.database'],
               read_default_group="client")

        cur = con.cursor(mdb.cursors.DictCursor)
        cur.execute(query)

        row_count = int(cur.rowcount)

        if(row_count > 0):
            rows = cur.fetchall()
        
    except mdb.Error, e:
        print "Error %d: %s" % (e.args[0],e.args[1])
        sys.exit(1)

    finally:
        if cur:
            cur.close()
        if con:
            con.close()

        return rows

def DBCallUpdate(proc_name, args = ()):
    query = "call %s(" % proc_name

    if len(args) > 1:
        i = 0
        while i < len(args):
            query += str(args[i])
            if i < len(args) - 1:
                query += ", "
            i += 1
    else:
        query = query + str(args[0])
    query = query + ")"
    con = None
    try:
        rows = None
        #Must specify read_default_group so that it will read my.cnf for the socket
        con = mdb.connect(host = settings['database.server'],
               user = settings['database.username'],
               passwd = settings['database.password'],
               db = settings['database.database'],
               read_default_group="client")

        cur = con.cursor(mdb.cursors.DictCursor)
        cur.execute(query)

    except mdb.Error, e:
        print "Error %d: %s" % (e.args[0],e.args[1])
        sys.exit(1)

    finally:
        if cur:
            cur.close()
        if con:
            con.commit()
            con.close()

#
# this function returns a list of users in the system
#
def getUserList():
    return DBCall('getUser', ['null', 'null', 'null', 'null', 'null', 'null', 'null', 'null', 'null'])

#
# This function returns the IDs of all active tasks
#
def getActiveTaskList():
    return DBCall('getTask', ['null', 'null', 'null', 'null', 'null', 'null', 'null', 'null', 'null', 'null', 'null'])

#
# This function returns the task identified by task_id
#
def getTaskById(task_id):
    return DBCall('getTask', [task_id, 'null', 'null', 'null', 'null', 'null', 'null', 'null', 'null', 'null', 'null'])

#
# This function returns all the tags related to a specific task
#
def getTaskTags(task_id):
    return DBCall('getTaskTags', [task_id])

#
# Return tag ids of tags liked by the user
#
def getUserTags(user_id):
    return DBCall('getUserTags', [int(user_id), 0])

#
# Get the time (in seconds) since the task was created
#
def getTaskActiveTimeSecs(created_time):
    created_time = time.strptime(str(created_time), "%Y-%m-%d %H:%M:%S")    # generate time_struct from string
    time_in_secs = time.mktime(created_time)    # convert time_struct to secs since epoch
    current_time = time.time()                  # get current time
    return current_time - time_in_secs

#
# Get the current score ofr the user-task pair
#
def getScoreForUserTask(user_id, task_id):
    result = DBCall('getUserTaskScore', [user_id, task_id])

    if(result != None):
        return result[0]['score']
    else:
        return result
    
def saveNewScore(user_id, task_id, score):
    previousScore = getScoreForUserTask(user_id, task_id)
    if(previousScore != int(score)):
        print "Updating score for user-task " + str(user_id) + "-" + str(task_id)  + " to " + str(score)
        DBCallUpdate('saveUserTaskScore', [user_id, task_id, score])

#
# Update the scores
#
start_time = time.time()
LoadConfig()
users = getUserList()       #get a list of all users
for user in users:
    #Get the tags that user has subscribed to   
    user_tags = getUserTags(user['user_id'])
    if(len(sys.argv) < 2):
        #Get all active tasks
        tasks = getActiveTaskList()
    else:
        #Get the task_id from the command line
        tasks = getTaskById(sys.argv[1])

    for task in tasks:
        #Get tags related to this task
        task_tags = getTaskTags(task['id'])

        #Calculate the new score and save it to the DB
        score = 0

        #Finding matching tags and increment score
        if(user_tags != None and task_tags != None):
            increment_value = 100
            for user_tag in user_tags:
                for task_tag in task_tags:
                    if(user_tag['tag_id'] == task_tag['tag_id']):
                        score += increment_value
                        increment_value *= 0.75

        #Check if the task is in the user's native language
        user_language = user['native_lang_id']
        user_region = user['native_region_id']
        if(user_language != None):
            if(task['source_id'] == user_language or task['target_id'] == user_language):
                score += 300
                if(task['sourceCountry'] == user_region or task['targetCountry'] == user_region):
                    score += 100

        #Increase score for older tasks
        task_time = getTaskActiveTimeSecs(task['created_time'])
        if(task_time > 0):
            task_time /= 60     # convert to minutes
            task_time /= 60     # convert to hours
            task_time /= 24     # convert to days
            score += math.floor(task_time)


        #Save the score to the DB if it has changed
        saveNewScore(user['user_id'], task['id'], score)

end_time = time.time()
running_time = end_time - start_time
seconds = running_time % 60
running_time /= 60
minutes = running_time % 60
running_time /= 60
hours = running_time % 24
time_string = ""
if(hours > 1):
    time_string += str(hours) + " hours, "
if(minutes > 1):
    time_string += str(minutes) + " minutes and "
time_string += str(seconds) + " seconds"
print "Total Running Time"
print "==========================="
print time_string
