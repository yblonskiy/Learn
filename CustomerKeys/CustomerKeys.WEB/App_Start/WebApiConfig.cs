using CustomerKeys.BLL.Interfaces;
using CustomerKeys.BLL.Services;
//using CustomerKeys.WEB.Interfaces;
using System;
using System.Collections.Generic;
using System.Web.Http;
using System.Web.Http.Dependencies;
using Unity;
using Unity.Lifetime;

namespace CustomerKeys.WEB
{
    public static class WebApiConfig
    {
        public static void Register(HttpConfiguration config)
        {           
            // Web API routes
            config.MapHttpAttributeRoutes();

            config.Routes.MapHttpRoute(
                name: "DefaultApi",
                routeTemplate: "api/{controller}/{id}",
                defaults: new { id = RouteParameter.Optional }
            );
        }

    }
       
}
