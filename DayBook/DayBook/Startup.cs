using ApplicationCore.Interfaces;
using AutoMapper;
using DayBook.Application.Interfaces;
using DayBook.Application.Services;
using DayBook.Web;
using DayBook.Web.Mapping;
using Infrastructure;
using Infrastructure.Identity;
using Infrastructure.Repository;
using Microsoft.AspNetCore.Authentication.JwtBearer;
using Microsoft.AspNetCore.Builder;
using Microsoft.AspNetCore.Hosting;
using Microsoft.AspNetCore.Http;
using Microsoft.AspNetCore.Identity;
using Microsoft.AspNetCore.Mvc;
using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Configuration;
using Microsoft.Extensions.DependencyInjection;
using Microsoft.IdentityModel.Tokens;
using System;
using System.Text;
using DayBook.Application.Auth;
using DayBook.Application.Helpers;

namespace DayBook
{
    public class Startup
    {
        public Startup(IConfiguration configuration)
        {
            Configuration = configuration;
        }

        public IConfiguration Configuration { get; }

        // This method gets called by the runtime. Use this method to add services to the container.
        public void ConfigureServices(IServiceCollection services)
        {
            services.AddDistributedSqlServerCache(options =>
            {
                options.ConnectionString = Configuration.GetConnectionString("DefaultConnection");
                options.SchemaName = "dbo";
                options.TableName = "SQLSessions";
            });

            services.AddSession(options =>
            {
                options.Cookie.Name = ".DayBook.Sessions";
                options.IdleTimeout = TimeSpan.FromMinutes(30);
            });

            services.AddDbContext<AppIdentityDbContext>(options =>
             options.UseSqlServer(Configuration.GetConnectionString("DefaultConnection")));


            services.AddIdentity<ApplicationUser, IdentityRole>(options =>
           {
               options.Stores.MaxLengthForKeys = 128;

               // Password settings
               options.Password.RequireDigit = false;
               options.Password.RequireLowercase = false;
               options.Password.RequireNonAlphanumeric = false;
               options.Password.RequireUppercase = false;
               options.Password.RequiredLength = 6;
               options.User.AllowedUserNameCharacters = null;

               // Confirmation email required for new account
               options.SignIn.RequireConfirmedEmail = false;

           })
                .AddEntityFrameworkStores<AppIdentityDbContext>()
            .AddDefaultTokenProviders();

            // Add initial data in database
            services.AddTransient<DbInitializer>();

            services.AddSingleton<IJwtFactory, JwtFactory>();

            // jwt wire up
            // Get options from app settings
            var jwtAppSettingOptions = Configuration.GetSection(nameof(JwtIssuerOptions));

            // Configure JwtIssuerOptions
            services.Configure<JwtIssuerOptions>(options =>
            {
                options.Issuer = jwtAppSettingOptions[nameof(JwtIssuerOptions.Issuer)];
                options.Audience = jwtAppSettingOptions[nameof(JwtIssuerOptions.Audience)];
                options.SecretKey = jwtAppSettingOptions[nameof(JwtIssuerOptions.SecretKey)];
                options.SigningCredentials = new SigningCredentials(new SymmetricSecurityKey(Encoding.ASCII.GetBytes(jwtAppSettingOptions[nameof(JwtIssuerOptions.SecretKey)])), SecurityAlgorithms.HmacSha256);
            });

            var tokenValidationParameters = new TokenValidationParameters
            {
                ValidateIssuer = true,
                ValidIssuer = jwtAppSettingOptions[nameof(JwtIssuerOptions.Issuer)],

                ValidateAudience = true,
                ValidAudience = jwtAppSettingOptions[nameof(JwtIssuerOptions.Audience)],

                ValidateIssuerSigningKey = true,
                IssuerSigningKey = new SymmetricSecurityKey(Encoding.ASCII.GetBytes(jwtAppSettingOptions[nameof(JwtIssuerOptions.SecretKey)])),

                RequireExpirationTime = false,
                ValidateLifetime = true,
                ClockSkew = TimeSpan.Zero
            };

            services.AddAuthentication(options =>
            {
                options.DefaultAuthenticateScheme = JwtBearerDefaults.AuthenticationScheme;
                options.DefaultChallengeScheme = JwtBearerDefaults.AuthenticationScheme;

            }).AddJwtBearer(configureOptions =>
            {
                configureOptions.ClaimsIssuer = jwtAppSettingOptions[nameof(JwtIssuerOptions.Issuer)];
                configureOptions.TokenValidationParameters = tokenValidationParameters;
                configureOptions.SaveToken = true;
            });

            // api user claim policy
            services.AddAuthorization(options =>
            {
                options.AddPolicy("ApiUser", policy => policy.RequireClaim(Constants.Strings.JwtClaimIdentifiers.Rol, Constants.Strings.JwtClaims.ApiAccess));
            });



            ////Configure JWT Token Authentication
            //services.AddAuthentication(auth =>
            //{
            //    auth.DefaultAuthenticateScheme = JwtBearerDefaults.AuthenticationScheme;
            //    auth.DefaultChallengeScheme = JwtBearerDefaults.AuthenticationScheme;
            //})
            //.AddJwtBearer(token =>
            //{
            //    token.RequireHttpsMetadata = false;
            //    token.SaveToken = true;
            //    token.TokenValidationParameters = new TokenValidationParameters
            //    {
            //        ValidateIssuerSigningKey = true,
            //        IssuerSigningKey = new SymmetricSecurityKey(Encoding.ASCII.GetBytes(jwtAppSettingOptions[nameof(JwtIssuerOptions.SecretKey)])),
            //        ValidateIssuer = true,
            //        ValidIssuer = jwtAppSettingOptions[nameof(JwtIssuerOptions.Audience)],
            //        ValidateAudience = true,
            //        ValidAudience = jwtAppSettingOptions[nameof(JwtIssuerOptions.Audience)],
            //        RequireExpirationTime = true,
            //        ValidateLifetime = true,
            //        ClockSkew = TimeSpan.Zero
            //    };
            //});

            //// api user claim policy
            //services.AddAuthorization(options =>
            //{
            //    options.AddPolicy("ApiUser", policy => policy.RequireClaim(Constants.Strings.JwtClaimIdentifiers.Rol, Constants.Strings.JwtClaims.ApiAccess));
            //});



            //services.AddAuthentication(options =>
            //{
            //    options.DefaultAuthenticateScheme = IdentityConstants.ApplicationScheme;
            //    options.DefaultChallengeScheme = IdentityConstants.ApplicationScheme;
            //}).AddCookie(options =>
            //    {
            //        options.LoginPath = "/Account/Login";
            //        options.AccessDeniedPath = "/Account/AccessDenied";
            //        options.LogoutPath = "/Account/Logout";
            //        options.Cookie.HttpOnly = true;
            //    });



            services.AddMvc().SetCompatibilityVersion(CompatibilityVersion.Version_2_1);
            
            services.AddScoped(typeof(IRepository<>), typeof(EntityBaseRepository<>));

            services.AddScoped<IManageService, ManageService>();
            services.AddScoped<IAccountService, AccountService>();
            services.AddScoped<IRecordService, RecordService>();
            services.AddScoped<IEmailSender, EmailSender>();

            services.AddHostedService<ConsumeScopedManageHostedService>();
            services.AddScoped<IScopedProcessingService, ScopedProcessingService>();

            services.AddAutoMapper(new Type[] { typeof(MappingProfile) });
        }

        // This method gets called by the runtime. Use this method to configure the HTTP request pipeline.
        public void Configure(IApplicationBuilder app, IHostingEnvironment env, DbInitializer seeder)
        {
            if (env.IsDevelopment())
            {
                app.UseDeveloperExceptionPage();
            }
            else
            {
                app.UseHsts();
            }
                       
            app.UseSession();

            app.Use(async (context, next) =>
                      {
                          var JWToken = context.Session.GetString("JWToken");
                          if (!string.IsNullOrEmpty(JWToken))
                          {
                              //context.Request.Headers.Add("Authorization", "Bearer " + JWToken);
                          }
                          await next();
                      });

            app.UseStaticFiles();
            app.UseCookiePolicy();
            app.UseHttpsRedirection();
            app.UseAuthentication();

            app.UseMvc(routes =>
            {
                routes.MapRoute(
                    name: "default",
                    template: "{controller=Home}/{action=Index}/{id?}");
            });

            seeder.InitializeData().Wait();
        }

    }

}
