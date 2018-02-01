# Solution
- The solution will be a client server model
  - The client being the mobile app where the users interract with the system
  - The client will be able able to perform all the task necessary from any of the interfaces that are provided
  - The server will house all the necesary business logic and data store necessary for the solution
  - The server responds to the requests from the client

## Architecture



## Schema

- users
  - questions
  - answers
  - attempts
  - options

  - users
    - Attributes
      - id { number,required }
      - email { string,required }
      - password { string,required }
      - name { string,required }
      - profile_photo { string }
      - remember_token {string}
      - facebook_id { string }
      - created_at { date }
      - updated_at { date }
  - questions
    - Attributes
      - id { number,required }
      - question { string,required }
      - created_at { date }
      - updated_at { date }
  - options
    - Attributes
      - id { number,required }
      - question_id { number,required }
      - answer { string,required }
      - is_correct_answer { boolean, required }
      - created_at { date }
      - updated_at { date }
  - attempts
    - Attributes
      - id { number,required }
      - user_id { number,required }
      - time_started { date,required }
      - time_ended { date,required }
      - duration { number }
      - score { number }
      - total_questions { number }
      - total_correct { number }
      - total_wrong { number }
      - created_at { date }
      - updated_at { date }
  - answers
    - Attributes
      - id { number,required }
      - user_id { number,required }
      - attempt_id { number, required }
      - question_id { number,required }
      - answer_id { number,required }
      - score { boolean,required }
      - created_at { date }
      - updated_at { date }

## EndPoints

Base Url - https://api.invoice.ng/api
Sign Up
  - url /auth/signup
  - POST
  - $request = [
        'headers' => ['Content-Type: application/json'],    
        'url' => '/signup',
        'params' => json_encode([
            'email'     => 'john@doe.com',
            'password'     => 'abx123xyz',
            'fullname'  => 'John Doe'
        ])
    ];
  - $response - 200 OK = {
          "status": true,
          "message": "User created",
          "data": {
            "userId": 1,
            "email": "john@doe.com",
            "fullName": "John Doe"
            "dateCreated": "2016-03-29T20:03:09.584Z",
            "dateModified": "2016-03-29T20:03:09.584Z",
            "profilePhoto": "",
            "facebookId": "" 
          }
        }

Sign In
  - url /auth/login
  - POST
  - $request = [
        'headers' => ['Content-Type: application/json'],    
        'url' => '/signin',
        'params' => json_encode([
            'email'     => 'john@doe.com',
            'password'     => 'abx123xyz'
        ])
    ];
  - $response - 200 OK = {
      "status":"success",
      "message":"User authenticated",
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjYzLCJpc3MiOiJodHRwczpcL1wvYXBpLmludm9pY2UubmdcL2F1dGhlbnRpY2F0ZSIsImlhdCI6MTQ5Nzg3NDU4NCwiZXhwIjoxNDk3ODgxNzg0LCJuYmYiOjE0OTc4NzQ1ODQsImp0aSI6InVOajVIN05VbmtkZm1GN2MifQ.lqzV8n71xBtkTqi4jjup4_lpEEn-RYsdLSL0S_EGots",
        "type": "bearer",
        "expiry": 1497881784,
      "data": {
            "userId": 1,
            "email": "john@doe.com",
            "fullName": "John Doe"
            "dateCreated": "2016-03-29T20:03:09.584Z",
            "dateModified": "2016-03-29T20:03:09.584Z",
            "profilePhoto": "",
            "facebookId": "" 
          }
  }

Facebook
  - url /facebook
  - POST
  - $request = [
        'headers' => ['Content-Type: application/json'],    
        'url' => '/signin',
        'params' => json_encode([
            'facebook_id'     => 'john@doe.com',
            'email'     => 'abx123xyz',
            'name'     => 'abx123xyz',
            'profile_photo' => ''
        ])
    ];
  - $response - 200 OK = {
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOjYzLCJpc3MiOiJodHRwczpcL1wvYXBpLmludm9pY2UubmdcL2F1dGhlbnRpY2F0ZSIsImlhdCI6MTQ5Nzg3NDU4NCwiZXhwIjoxNDk3ODgxNzg0LCJuYmYiOjE0OTc4NzQ1ODQsImp0aSI6InVOajVIN05VbmtkZm1GN2MifQ.lqzV8n71xBtkTqi4jjup4_lpEEn-RYsdLSL0S_EGots",
        "type": "bearer",
        "expiry": 1497881784
  }

Questions
  - url /quiz/questions
  - GET
  
  - $response - 200 OK = {
        "status": "success",
        "data": [
            {
                "id": "787714923851009",
                "question": "Who is the president of Nigeria", 
                "dateModified": "2017-06-06 11:11:07"
                "options":[
                  {
                    "id": "1",
                    "answer": "Goodluck Ebele Jonathan",
                    
                },
                {
                    "id": "2",
                    "answer": "M Buhari",
                    
                },
                ...
                ]
            },
            ...
        ]
    }

Submit
  - url /quiz/submit
  - POST
  - $request = [
        'headers' => ['Content-Type: application/json'],    
        'url' => '/quiz/submit',
        'params' => json_encode([
          "time_started"=>"2018-01-01 00:00",
            "time_ended"=>"2018-01-02 01:20",  
            'questions'=>[
              [
                "id"=>1,
                "option_id"=>1
              ],
              ...
            ]
            
        ])
    ];
  - $response - 200 OK = {
        "status": "success",
        "message": "Quiz finished",
         "data": {
              "time_started": "2018-01-01 00:00",
              "time_ended": "2018-01-02 01:20",
              "duration": 1520,
              "total_questions": 1,
              "user_id": 15,
              "updated_at": "2018-02-01 07:11:02",
              "created_at": "2018-02-01 07:11:02",
              "id": 26,
              "score": 0,
              "total_correct": 0,
              "total_wrong": 1,
              "answer": [
                  {
                      "id": 3,
                      "user_id": 15,
                      "attempt_id": 26,
                      "question_id": 1,
                      "option_id": 1,
                      "is_correct": 0,
                      "created_at": "2018-02-01 07:11:02",
                      "updated_at": "2018-02-01 07:11:02"
                  }
              ]
          }
    }