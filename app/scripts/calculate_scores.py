#!/usr/bin/python

import MySQLdb as mdb
import sys
import ConfigParser
import string
from sets import Set

con = None
settings = dict()

#
# Load the configuration file that stores the Database info
#
def LoadConfig():
    file_name = "../includes/conf.ini"
    parser = ConfigParser.ConfigParser()
    parser.read(file_name)
    for section in parser.sections():
        name = string.lower(section)
        for opt in parser.options(section):
            settings[name + "." + string.lower(opt)] = string.strip(parser.get(section, opt)).replace("\"", '').replace("\'", '')

#
# this function returns a list of users in the system
#
def getUserList():
    try:
        rows = None
        con = mdb.connect(unix_socket = "/opt/lampp/var/mysql/mysql.sock",
               host = 'localhost',
               user = 'test_user',
               passwd = 'password',
               db = 'SolasMatch')
    
        cur = con.cursor(mdb.cursors.DictCursor)
        query = "SELECT * FROM user"
        cur.execute(query)

        row_count = int(cur.rowcount)

        if(row_count > 0):
            rows = cur.fetchall()
        
    except mdb.Error, e:
        print "Error %d: %s" % (e.args[0],e.args[1])
        sys.exit(1)

    finally:
        if con:
            con.close()

        return rows
#
# This function returns the IDs of all active tasks
#
def getActiveTaskList():
    try:
        result = None
        con = mdb.connect(unix_socket = "/opt/lampp/var/mysql/mysql.sock",
                host = 'localhost',
                user = 'test_user',
                passwd = 'password',
                db = 'SolasMatch')
         
        cur = con.cursor(mdb.cursors.DictCursor)
        query = "SELECT id FROM task"
        cur.execute(query)

        result = cur.fetchall()
    except:
        print "Error %d: %s" % (e.args[0],e.args[1])
        sys.exit(1)

    finally:
        if con:
            con.close()

        return result

#
# This function returns all the tags related to a specific task
#
def getTaskTags(task_id):
    try:
        result = None
        con = mdb.connect(unix_socket = "/opt/lampp/var/mysql/mysql.sock",
                host = 'localhost',
                user = 'test_user',
                passwd = 'password',
                db = 'SolasMatch')
         
        cur = con.cursor(mdb.cursors.DictCursor)
        query = "SELECT tag_id FROM task_tag WHERE task_id = %d" % int(task_id)
        cur.execute(query)

        result = cur.fetchall()
    except:
        print "Error %d: %s" % (e.args[0],e.args[1])
        sys.exit(1)

    finally:
        if con:
            con.close()

        return result    

#
# Return tag ids of tags liked by the user
#
def getUserTags(user_id):
    try:
        result = None
        con = mdb.connect(unix_socket = "/opt/lampp/var/mysql/mysql.sock",
                host = 'localhost',
                user = 'test_user',
                passwd = 'password',
                db = 'SolasMatch')
         
        cur = con.cursor(mdb.cursors.DictCursor)
        query = "SELECT tag_id FROM user_tag WHERE user_id = %d" % int(user_id)
        cur.execute(query)

        result = cur.fetchall()
    except:
        print "Error %d: %s" % (e.args[0],e.args[1])
        sys.exit(1)

    finally:
        if con:
            con.close()

        return result

#
# Get the current score ofr the user-task pair
#
def getScoreForUserTask(user_id, task_id):
    try:
        result = None
        con = mdb.connect(unix_socket = "/opt/lampp/var/mysql/mysql.sock",
                host = 'localhost',
                user = 'test_user',
                passwd = 'password',
                db = 'SolasMatch')
         
        cur = con.cursor(mdb.cursors.DictCursor)
        query = "SELECT score FROM user_task_score WHERE user_id = %d AND task_id = %d" % (int(user_id), int(task_id))
        cur.execute(query)

        if(cur.rowcount > 0):
            result = cur.fetchall()
    except:
        print "Error %d: %s" % (e.args[0],e.args[1])
        sys.exit(1)

    finally:
        if con:
            con.close()
        if(result != None):
            return result[0]['score']
        else:
            return result
    
def saveNewScore(user_id, task_id, score):
    previousScore = getScoreForUserTask(user_id, task_id)
    if(previousScore != int(score)):
        print "Updating score for user-task " + str(user_id) + "-" + str(task_id)  + " to " + str(score)
        if(previousScore != None):
            query = "UPDATE user_task_score SET score=%d WHERE user_id=%d AND task_id=%d" % (int(score), int(user_id), int(task_id))
        else:
            query = "INSERT INTO user_task_score (user_id, task_id, score) VALUES (%d, %d, %d)" % (int(user_id), int(task_id), int(score))
        try:
            result = None
            con = mdb.connect(unix_socket = "/opt/lampp/var/mysql/mysql.sock",
                    host = 'localhost',
                    user = 'test_user',
                    passwd = 'password',
                    db = 'SolasMatch')
         
            cur = con.cursor(mdb.cursors.DictCursor)
            cur.execute(query)

        except:
            print "Error %d: %s" % (e.args[0],e.args[1])
            sys.exit(1)

        finally:
            if con:
                con.commit()
                con.close()
    
            return result

#
# Update the scores
#
LoadConfig()
users = getUserList()       #get a list of all users
for user in users:
    #Get the tags that user has subscribed to   
    user_tags = getUserTags(user['user_id'])
    #Get all active tasks
    tasks = getActiveTaskList()

    for task in tasks:
        #Get tags related to this task
        task_tags = getTaskTags(task['id'])

        #Calculate the new score and save it to the DB
        score = 0
        increment_value = 100
        for user_tag in user_tags:
            for task_tag in task_tags:
                if(user_tag == task_tag):
                    score += increment_value
                    increment_value *= 0.75

        saveNewScore(user['user_id'], task['id'], score)

