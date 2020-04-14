using System;
using System.IO;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Net;
using System.Web;

namespace VRPAPIExample
{
    class Program
    {
        static string jsonPath = "../../../json";
        static string baseApiURL = "http://vrp.viamente.com/api/vrp/v2";
        static string key = "YOUR_API_KEY_HERE";

        static void Main(string[] args)
        {

            dynamic reqCreateNewRouteplan = getObjectFromJsonFile("create_new_routeplan_request.json");

            Console.WriteLine("List available subfleets");
            string subfleetsRes = executeRest(baseApiURL+"/subfleets", "GET");

            Console.WriteLine("List saved routeplans");
            executeRest(baseApiURL+"/routeplans", "GET");

            // For example purposes: get the ID of the first returned subfleet and use that when creating the new routeplan
            // NOTE: here we assume that the "List available subfleets" invocation was successul and that there is at least one saved subfleet.
            dynamic subfleets = jsonDecode(subfleetsRes);
            string subfleetID = subfleets["subfleets"][0]["id"];
            reqCreateNewRouteplan["subfleetID"] = subfleetID;

            Console.WriteLine("Create new routeplan");
            string routeplanRes = executeRest(baseApiURL+"/routeplans", "POST", jsonEncode(reqCreateNewRouteplan));

            string routeplanID = jsonDecode(routeplanRes)["id"];

            Console.WriteLine("Get details about newly created routeplan");
            executeRest(baseApiURL+"/routeplans/"+routeplanID, "GET");

            Console.WriteLine("List saved routeplans");
            executeRest(baseApiURL+"/routeplans", "GET");

            Console.WriteLine("Press Enter to terminate");
            Console.ReadLine();
        }

        //
        // Helper methods
        //

        static string executeRest(string url, string method)
        {
            return executeRest(url, method, "");
        }

        static string executeRest(string url, string method, string postData) 
        {
            // Setup request
            WebRequest request = WebRequest.Create(url + "?key=" + key);
            request.Method = method;
            byte[] vbyteArray = Encoding.UTF8.GetBytes(postData);
            request.ContentType = "application/json";
            request.ContentLength = vbyteArray.Length;
            if (method == "POST" || method == "PUT")
            {
                Console.WriteLine("Request body: \n" + postData);
                Stream dataStreamIn = request.GetRequestStream();
                dataStreamIn.Write(vbyteArray, 0, vbyteArray.Length);
                dataStreamIn.Close();
            }
            WebResponse response = null;
            try
            {
                response = request.GetResponse();
            }
            catch (WebException we)
            {
                // NOTE: GetResponse() throws an exception if the returned HTTP code is != 200.
                // Since we must handle non-200 responses, we have to hack our way around this despicable behaviour
                response = we.Response;
            }
            // Display response data
            Console.WriteLine("Response status: " + ((int) ((HttpWebResponse)response).StatusCode));
            Stream dataStreamOut = response.GetResponseStream();
            StreamReader reader = new StreamReader(dataStreamOut);
            string responseFromServer = reader.ReadToEnd();
            Console.WriteLine("Response body: \n" + responseFromServer);
            Console.WriteLine("\n");
            // Clean up the streams.
            reader.Close();
            dataStreamOut.Close();
            response.Close();
            return responseFromServer;

        }

        static dynamic getObjectFromJsonFile(string filePath)
        {
            StreamReader streamReader = new StreamReader(jsonPath + "/" + filePath);
            string jsonStr = streamReader.ReadToEnd();
            streamReader.Close();
            return jsonDecode(jsonStr);
        }

        static dynamic jsonDecode(string json)
        {
            var jsonSerializer = new System.Web.Script.Serialization.JavaScriptSerializer();
            return jsonSerializer.DeserializeObject(json);
        }

        static string jsonEncode(dynamic jsonObj)
        {
            var jsonSerializer = new System.Web.Script.Serialization.JavaScriptSerializer();
            return jsonSerializer.Serialize(jsonObj);
        }

    }
}
