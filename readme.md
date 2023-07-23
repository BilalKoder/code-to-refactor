1. ### Overview
I've changed the code. I think there will be many of things that can be improved or addressed. Because it was already addressed earlier, I didn't update the majority of the spots where the same or comparable things are happening.
Below are details.

2. ### My Take
While writing the code, there are several things on which I personally try to concentrate and pay attention.
Certain of them are absent from the original code.

I discover that exception handling is lacking and that it should be there for proper error management. Where appropriate, certain exceptions can also be established. The try-catch block is the same. Data manipulation should be done via transactions. Short functions that adhere to the S (single responsibility) for SOLID principles are preferred. If you read through the function, you shouldn't have any trouble remembering what you were searching for because it should be of a respectable length. You should be able to understand the purpose of a function without actually seeing its implementation if the method name is descriptive.

I believe that managing APIs can be done in a more systematic and organised manner that adheres to REST principles.
Instead of using response(), which may provide a json response but requires an extra step, use response()->json() to transmit the data directly including any necessary status codes. Use of JsonResource, which was quite useful, is one approach. return new BookingResource($data), for instance. In my commit, I also included the file as an example.

I discovered the superfluous code that might be placed into a function. I believe that logic or code should be transferred to a function if it has to be written more than once. The usage of else blocks is unnecessary. In code, attempting to avoid else makes things appear much cleaner. I have withdrawn myself from a lot of areas, but there are still a lot of places that might be made easier. 