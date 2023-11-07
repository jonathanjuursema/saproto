<script setup lang="ts">
import moment from 'moment';
</script>

<template>
  <div class="w-full bg-back-dark text-white p-4 flex flex-col items-center">
    <div
      class="flex flex-col space-y-8 items-stretch justify-between md:flex-row md:space-y-0 md:w-full lg:mx-auto lg:w-10/12 xl:w-8/12"
    >
      <div>
        <strong>
          <span class="fas fa-home"></span>&nbsp;&nbsp;
          <a class="underline" href="https://www.saproto.nl/">S.A. Proto</a>
        </strong>
        <br />
        <p>Zilverling A230</p>
        <p>Drienerlolaan 5</p>
        <p>7522NB Enschede</p>
      </div>

      <div>
        <span class="fas fa-clock fa-fw"></span>&nbsp;&nbsp;Monday & Friday 08:30-16:00<br />
        <span class="fas fa-clock fa-fw"></span>&nbsp;&nbsp;Tuesday-Thursday, 08:30-17:30<br />
        <span class="fas fa-phone fa-fw"></span>&nbsp;&nbsp;<a class="tel text-white" href="tel:+31534894423">
          +31 (0)53 489 4423 </a
        ><br />
        <span class="fas fa-envelope fa-fw"></span>&nbsp;&nbsp;
        <a class="underline" href="mailto:board@proto.utwente.nl">board@proto.utwente.nl</a>
      </div>

      <div>
        <a class="underline" href="https://www.youtube.com/channel/UCdH2x3ObOrmLdm2OOGDBslw" target="_blank">
          YouTube&nbsp;&nbsp;<i class="fab fa-fw fa-youtube" aria-hidden="true"></i></a
        ><br />
        <a class="underline" href="https://www.linkedin.com/company/s-a-proto/" target="_blank">
          LinkedIn&nbsp;&nbsp;<i class="fab fa-fw fa-linkedin" aria-hidden="true"></i></a
        ><br />
        <a class="underline" href="https://www.instagram.com/s.a.proto/" target="_blank">
          Instagram&nbsp;&nbsp;<i class="fab fa-fw fa-instagram" aria-hidden="true"></i></a
        ><br />
        <a class="underline" href="https://www.proto.utwente.nl/page/privacy" target="_blank">
          Privacy Policy&nbsp;&nbsp;<i class="fa fa-shield" aria-hidden="true"></i></a
        ><br />
      </div>

      <div>
        <img class="h-20" src="/images/logo/inverse.png" />
      </div>
    </div>
    <p class="text-center mt-3 mb-2">
      <sub>
        &copy; {{ moment().year() }} S.A. Proto. All rights reserved.
        <span class="d-sm-none">
          Please familiarize yourself with our
          <a href="https://wiki.proto.utwente.nl/ict/privacy/start?do=export_pdf" target="_blank" class="text-white">
            privacy policy
          </a>
          and
          <a href="https://wiki.proto.utwente.nl/ict/responsible-disclosure" class="text-white">
            responsible disclosure policy </a
          >.
        </span>
        The website source is available on
        <a href="https://github.com/saproto/saproto" target="_blank" class="text-white"> GitHub </a>.
        <br />
        This website has been created with <i class="fas fa-heart text-primary"></i> by the folks of the
        <a href="{{ route('developers') }}" class="underline"> Have You Tried Turning It Off And On Again committee </a
        >.
      </sub>
    </p>
  </div>
</template>

<style scoped></style>
