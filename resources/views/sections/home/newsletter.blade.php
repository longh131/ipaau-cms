        <section
          data-type="newsletter"
          data-index="13"
          class="py-12 overflow-hidden bg-[color:var(--bg-color)]"
          style="
            background-image: url('{{ asset('assets/img/graphics_base.png') }}');
            background-size: 100% 100%;
            background-position: center;
            background-repeat: no-repeat;
            --bg-color: transparent;
            --ipa-color-light: oklch(0.464 0 0);
            --ipa-color-dark: oklch(1 0 0);
            --light-or-dark: light;
            color: var(--ipa-color-light);
          "
        >
          <div
            class="inner container px-4 md:px-10 mx-auto flex flex-col gap-12"
          >
            <div class="container mx-auto px-4 py-16 lg:py-20">
              <div
                class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-start"
              >
                <div class="content-section">
                  <div>
                    <div class="text-center container mx-auto">
                      <div
                        data-type="section-title"
                        data-rte="true"
                        class="font-apex-book"
                        style="
                          --ipa-color-light: oklch(0.3152 0.1176 262.41);
                          --ipa-color-dark: oklch(0.9011 0.0552 218.07);
                          color: var(--ipa-color-light);
                        "
                      >
                        <div>
                          <h4
                            class="text-display-xl lg:text-display-2xl text-display-lg lg:text-display-xl"
                          >
                            Subscribe to our newsletter
                          </h4>
                        </div>
                      </div>
                      <div
                        class="text-[color:var(--ipa-color)] mt-8 text-xl font-din"
                        data-type="section-description"
                        data-rte="true"
                        style="
                          --ipa-color-light: oklch(0.464 0 0);
                          --ipa-color-dark: oklch(0.9612 0 0);
                          color: var(--ipa-color-light);
                        "
                      >
                        <div>
                          <h1>
                            Join our 2,000 subscribers to get the latest updates
                            delivered straight to your inbox—no spam, just the
                            good stuff.
                          </h1>
                          <div>
                            <ul>
                              <li>
                                Stay informed with the latest news, trends, and
                                tips
                              </li>
                              <li>
                                Quick reads designed to fit your busy schedule
                              </li>
                              <li>No spam—ever, just the good stuff</li>
                            </ul>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="form-section relative">
                  <form class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                    <div>
                      <label for="fullName"
                        >姓名<span class="text-error-bold ml-1"
                          >*</span
                        ></label
                      ><input
                        type="text"
                        id="fullName"
                        name="fullName"
                        required=""
                        value=""
                      />
                    </div>
                    <div>
                      <label for="phone"
                        >手机号<span class="text-error-bold ml-1"
                          >*</span
                        ></label
                      ><input
                        type="tel"
                        id="phone"
                        name="phone"
                        required=""
                        value=""
                      />
                    </div>
                    <div class="lg:col-span-2">
                      <label for="email"
                        >邮箱<span class="text-error-bold ml-1"
                          >*</span
                        ></label
                      ><input
                        type="email"
                        id="email"
                        name="email"
                        required=""
                        value=""
                      />
                    </div>
                    <div>
                      <label for="company">公司</label
                      ><input
                        type="text"
                        id="company"
                        name="company"
                        value=""
                      />
                    </div>
                    <div>
                      <label for="jobTitle">现任职务</label
                      ><input
                        type="text"
                        id="jobTitle"
                        name="jobTitle"
                        value=""
                      />
                    </div>
                    <div class="lg:col-span-2">
                      <label for="education">第一高等学历</label
                      ><input
                        type="text"
                        id="education"
                        name="education"
                        value=""
                      />
                    </div>
                    <p class="label-xs">* 必填项</p>
                    <div class="flex justify-center lg:justify-end">
                      <button
                        type="submit"
                        class="max-sm:w-full cta group font-medium uppercase border-2 border-link bg-link text-white hover:bg-link-hover hover:border-link-hover focus-visible:bg-link-hover focus-visible:border-link-focused focus-visible:outline-[4px] focus-visible:outline-link-focused focus-visible:outline focus-visible:ring-transparent disabled:bg-disabled disabled:border-disabled disabled:text-grey disabled:hover:no-underline disabled:cursor-not-allowed flex transition-all duration-300 border uppercase text-lg hover:underline focus-visible:underline px-[24px] py-[11.5px] sm:px-[32px] sm:py-[15.5px] rounded-full"
                        data-idx="0"
                      >
                        <div class="flex flex-wrap items-center w-full">
                          <div
                            class="cta-content flex flex-nowrap items-center justify-center w-full uppercase"
                          >
                            SUBSCRIBE
                          </div>
                        </div>
                      </button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </section>
