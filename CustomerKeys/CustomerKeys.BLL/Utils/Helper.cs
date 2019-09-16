using System;
using System.Linq;
using System.Security.Cryptography;

namespace CustomerKeys.BLL.Utils
{
    public static class Helper
    {      
        /// <summary>
        /// Generates the key in XXXXXXXX-XXXXXXXX format 
        /// </summary>
        /// <returns></returns>
        public static string GetKey()
        {
            return String.Format("{0:D8}-{1:D8}", GetRandom(), GetRandom());
        }

        /// <summary>
        /// Generates a random 8 digits
        /// </summary>
        /// <returns></returns>
        private static uint GetRandom()
        {
            var bytes = new byte[4];
            var rng = RandomNumberGenerator.Create();
            rng.GetBytes(bytes);

            return BitConverter.ToUInt32(bytes, 0) % 100000000;
        }
    }
}