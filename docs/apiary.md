FORMAT: 1A
HOST: http://api.yquest.yanaminina.ru

# YQuest API

# URL paths

All Rest API methods must have prefix with the next format:

    - http://api.[api_stage].quest.yanaminina.ru/[version]/
    
All WS methods must have prefix with the next format:

    - ws://api.[api_stage].quest.yanaminina.ru:[port]/[version]  

where:
+ `api_stage` - could have one of the next values: `dev`, - or could be omitted in case of production
server.
+ `version` - API version in the next format: `v#`. For example, `v1`, `v2`, `v3`.
+ `port` - TCP-port.

Available REST APIs:
- `http://api.quest.yanaminina.ru/v1/` production API

Available WS:
- `ws://api.quest.yanaminina.ru:1025/v1` production


# Requests & Responses

Server conforms to REST standard. All requests and responses use JSON to represent data, except for calls related to binary data.

# Response Status

### Success

In case of success the server must return HTTP 200 code.

### Failure

In case of failure the server must return HTTP 4xx or 5xx code with response body containing information about an error.

Example:

    + 404 NOT FOUND
    
        {
            "status": 404001,
            "response": "Unknown Event",
        }

**Reference:**

|Code|Description|
|:---|:---|
|**Bad request (HTTP code 400):**|
|400001|Missed input parameter|
|400002|Wrong value (when parsing arguments or incorrect argument combination)|
|**Unauthorized (HTTP code 401):**|
|401001|Wrong Authorization token|
|401002|Wrong / inActive Google token|
|**Not Found (HTTP code 404):**|
|404001|Unknown Event|
|404002|Unknown Answer|
|**Internal Server Error (HTTP code 500):**|
|500000|Unknown error|

# Authorization

Authorization by request header.

    Authorization: < access_token >

Authorization is required for all API methods except of the /auth/sign-in-* methods.    

# Models

## Chapter

    + JSON
    
        {
            "chapter": {
                "id": "chapterId",
                "title":  "Title #1",
                "content": "Lorem Ipsum ..",
                "questions": [
                    {
                        // question fields
                    },
                    ...
                ],
                "lockCount": 123
            }
        }
        
        
## Question

    + JSON
    
        {
            "question": {
                "id": "questionId",
                "title":  "Title #1",
                "content": "Lorem Ipsum ..",
            }
        }


# WS Events: General Info
## Event Format

    + JSON
    
        {
            "topic": "main" OR "quest", // For Server Events
            "event": "eventName",
            "payload": {
                // payload data
            },
            "pid": 123
        }

# WS Events: Client
## Ping

    + JSON
    
        {
            "event": "ping",
            "payload": {},
            "pid": 1
        }

## Join

    + JSON
    
        {
            "event": "join",
            "payload": {
                "token": "<access_token>" // from sign-in header response
            },
            "pid": 1
        }

## Answer

     + JSON
    
        {
            "event": "answer",
            "payload": {
                "chapter_uid": "chapter_uid",
                "answer": "answer"
            },
            "pid": 1
        }
        
## Hint

     + JSON
    
        {
            "event": "hint",
            "payload": {
                "chapter_uid": "chapter_uid"
            },
            "pid": 1
        }        

# WS Events: Server
## Success/Error Reply

    + JSON
    
        {
            "topic": "main",
            "event": "reply",
            "payload": {
                "status": 200, // 404001, 401001, etc
                "response": "message"
            },
            "pid": 1
        }

## Answer

    + JSON
    
        {
            "topic": "quest",
            "event": "answer",
            "payload": {
                "status": 200 OR 404002,
                "response": "message"
            },
            "pid": 1
        }
        
## Hint

    + JSON
    
        {
            "topic": "quest",
            "event": "hint",
            "payload": {
                "status": 200,
                "response": "message"
            },
            "pid": 1
        }        
        
## StateContent

    + JSON
    
        {
            "topic": "quest",
            "event": "state_content",
            "payload": {
                "status": 200,
                "response":  {
                    "content": {
                        "chapters": [
                            {
                                // chapter fields
                            },
                            ...
                        ]
                    }
                }
            },
            "pid": 1
        }
        
## NewContent

    + JSON
    
        {
            "topic": "quest",
            "event": "new_content",
            "payload": {
                "status": 200,
                "response":  {
                    "content": {
                        "question": {
                                // question fields
                        }
                    }
                }
            },
            "pid": 1
        }



# Group Authorization
## Sign In Google [/auth/sign-in-google]
### Sign In Google [POST]
Errors: 400001, 401002

+ Request (application/json)

        {
            "token": "google_id_token"
        }
        
+ Response 200
    + Headers
            Authorization: adsfjkhafadfdaadfa
    + Body

            {
            }
            
+ Response 400
+ Response 401

# Group WS: Client Event
## Join [/join]
### Join in Quest [GET]
Client send JoinEvent

Server responds with Reply Event & StateContent Event (for success reply)

+ Response 200


## Ping [/ping]
### Ping Server [GET]
Client send PingEvent

Server responds with Reply Event

+ Response 200

## Answer [/answer]
### Answer for a question [GET]
Client send AnswerEvent

Server responds with Answer Event & NewQuestion Event (for success answer)

+ Response 200

## Hint [/hint]
### Get a hint [GET]
Client send HintEvent

Server responds with Hint Event

+ Response 200









