using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;

namespace YSD.Web.Util
{
    public class Helper
    {
        /// <summary>
        /// Generate MD5 sum based on on sourceString
        /// </summary>
        /// <param name="sourceString">input string</param>
        /// <returns></returns>
        public static string CalculateMD5Sum(string sourceString)
        {
            // step 1, calculate MD5 hash from input
            System.Security.Cryptography.MD5 md5 = System.Security.Cryptography.MD5.Create();
            byte[] inputBytes = Encoding.Default.GetBytes(sourceString);
            byte[] hash = md5.ComputeHash(inputBytes);

            // step 2, convert byte array to hex string
            StringBuilder sb = new StringBuilder();

            for (int i = 0; i < hash.Length; i++)
            {
                sb.Append(hash[i].ToString("X2"));
            }

            return sb.ToString();
        }
    }
}