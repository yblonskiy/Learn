﻿using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;

namespace CustomerKeys.Models
{
    public class Key
    {
        public int Id { get; set; }

        public string Value { get; set; }

        public int CustomerId { get; set; }
    }
}