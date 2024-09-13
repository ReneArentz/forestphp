<?php
/**
 * standard footer file of fphp framework
 *
 * @category    forestPHP Framework
 * @author      Rene Arentz <rene.arentz@forestany.net>
 * @copyright   (c) 2024 forestPHP Framework
 * @license     https://www.gnu.org/licenses/gpl-3.0.de.html GNU General Public License 3
 * @license     https://opensource.org/licenses/MIT MIT License
 * @version     1.1.0 stable
 * @link        https://forestany.net
 * @object-id   0x1 0001D
 * @since       File available since Release 0.1.0 alpha
 * @deprecated  -
 *
 * @version log Version			Developer	Date		Comment
 * 				0.1.0 alpha		renea		2019-08-04	first build
 * 				0.9.0 beta		renea		2020-01-27	added useful information only for RootUser
 * 				1.1.0 stable	renea		2024-08-06	added new global variable to write runtime information
 * 				1.1.0 stable	renea		2024-08-08	added new footer with link to top anchor, imprint and data protection
 * 				1.1.0 stable	renea		2024-08-11	introduction of cookie consent
 */
?>
<?php
global $b_write_runtime_infos;

/* write runtime infos if user is root and global bool is set true */
if ( ($o_glob->Security->RootUser) && ($b_write_runtime_infos) ) {
	global $start;
	$end = microtime(true);
	$f_runtime = $end - $start;
	
	echo '<div>' . "\n";
	echo '<pre>';
	echo '<hr>';
	echo $o_glob->GetTranslation('Runtime', 1) . ' ' . round($f_runtime, 3) . ' ' . $o_glob->GetTranslation('RuntimeSeconds', 1);
	echo '<hr>';
	echo $o_glob->Base->{$o_glob->ActiveBase}->AmountQueries . ' ' . $o_glob->GetTranslation('Queries', 1) . ' [' . $o_glob->Base->{$o_glob->ActiveBase}->BaseGateway . ']';
	echo '<hr>';
	echo 'memory_get_usage(false): ' . getNiceFileSize(memory_get_usage(false)) . '<br>';
	echo 'memory_get_usage(true): ' . getNiceFileSize(memory_get_usage(true)) . '<br>';
	echo 'memory_get_peak_usage(false): ' . getNiceFileSize(memory_get_peak_usage(false)) . '<br>';
	echo 'memory_get_peak_usage(true): ' . getNiceFileSize(memory_get_peak_usage(true)) . '<br>';
	
	global $b_write_sql_queries;

	if ($b_write_sql_queries) {
		echo '<hr>';
		foreach ($o_glob->Base->{$o_glob->ActiveBase}->Queries as $query) {
			echo $query . '<br>';
		}
	}
	
	echo '</pre>';
	echo '</div>' . "\n";
}
?>
	<footer class="footer mt-auto py-3 bg-dark">
		<div class="container">
			<span class="text-light me-2">&copy; <?php echo date("Y"); echo ' '; echo $o_glob->Trunk->NavbarBrandTitle; ?></span>

			<br class="responsive">

			<a class="btn btn-light btn-sm" href="#topAnchor">
				<span class="bi bi-arrow-up"></span>
			</a>
			
			<a class="btn btn-light btn-sm" href="#" data-bs-toggle="modal" data-bs-target="#LegalModal">
				<?php echo $o_glob->GetTranslation('Legal', 1); ?>
			</a>
			
			<a class="btn btn-light btn-sm" href="#" data-bs-toggle="modal" data-bs-target="#DataProtectionModal">
				<?php echo $o_glob->GetTranslation('DataProtection', 1); ?>
			</a>

			<br class="responsive">

			<span class="text-light ms-md-2">powered by forestPHP 1.1.0</span>
		</div>
	</footer>

	<div class="modal fade" id="LegalModal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header bg-dark text-light text-center">
					<h4 class="modal-title fs-4"><?php echo $o_glob->GetTranslation('Legal', 1); ?></h4>
					<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body bg-light-subtle">
					<p><b>Angaben gemäß § 5 TMG</b></p>
					<p><i><?php echo $o_glob->Trunk->NavbarBrandTitle; ?></i></p>
					<p>Street<br>ZIP-Code City, Country</p>
					<p>Mail</p>
					<p>
					E-Mail: 
					<?php
					/* mail string */
					$s_mail = 'info@test.net';
					/* print obfuscate mail string */
					echo \fPHP\Helper\forestStringLib::ObfuscateString($s_mail);
					?>
					</p>
					<p>
						<b>Vertreten durch:</b>
						<br><br>
						Max Mustermann
						<br><br>
						<b>Streitschlichtung und Verbraucherstreitbeilegung:</b>
						<br><br>
						Die Europäische Kommission stellt eine Plattform zur Online-Streitbeilegung (OS) bereit, die Sie unter <a href="https://ec.europa.eu/consumers/odr/" target="_blank">https://ec.europa.eu/consumers/odr/</a> finden. Verbraucher haben die Möglichkeit, diese Plattform für die Beilegung ihrer Streitigkeiten zu nutzen. Unsere E-Mail-Adresse finden Sie oben im Impressum.
						<br><br>
						Wir sind nicht bereit und nicht verpflichtet an einem Streitbeilegungsverfahren vor einer Verbraucherstreitschlichtungsstelle teilzunehmen.
					</p>
				</div>
				<div class="modal-footer bg-dark text-light">
					<button type="button" class="btn btn-light btn-default pull-right" data-bs-dismiss="modal"><?php echo $o_glob->GetTranslation('btnClose', 1); ?></button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="DataProtectionModal">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div class="modal-header bg-dark text-light text-center">
					<h4 class="modal-title fs-4"><?php echo $o_glob->GetTranslation('DataProtection', 1); ?></h4>
					<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body bg-light-subtle">
					<div id="contentDataProtectionModal">
						<a href="./" data-bs-toggle="modal" data-bs-target="#LegalModal">
							<?php echo $o_glob->GetTranslation('Legal', 1); ?>
						</a>
						<hr>
						Der Schutz Ihrer personenbezogenen Daten anlässlich Ihres Besuchs auf unserer Homepage ist uns ein wichtiges Anliegen. <?php echo $o_glob->GetTranslation('DataProtectionNotice', 1); ?> Ihre Daten werden im Rahmen der gesetzlichen Vorschriften geschützt. Wir möchten Sie nachfolgend über Art und den Umfang der Verarbeitung von personenbezogenen Daten über diese Website entsprechend Artikel 13 der Datenschutzgrundverordnung (DSGVO) unterrichten.
						<br><br>
						<b>1. Angaben zur Verantwortlichen Stelle</b><br>
						<i><?php echo $o_glob->Trunk->NavbarBrandTitle; ?></i><br>
						Street<br>
						Zip-Code City, Country<br>
						<br>
						E-Mail: <?php /* print obfuscate mail string */ echo \fPHP\Helper\forestStringLib::ObfuscateString($s_mail); ?>
						<br><br>
						<b>2. Datenverarbeitung über die Website</b><br>
						Ihr Besuch auf unserer Webseite wird protokolliert. Erfasst werden zunächst im Wesentlichen folgende Daten, die Ihr Browser an uns übermittelt:
						<ul>
							<li>die aktuell von Ihrem PC oder Ihrem Router verwendete IP-Adresse</li>
							<li>Datum und Uhrzeit</li>
							<li>Browsertyp und -Version</li>
							<li>das Betriebssystem Ihres PC</li>
							<li>die von Ihnen betrachteten Seiten</li>
							<li>Name und Größe der angefragten Datei(en)</li>
							<li>sowie ggf. die URL der verweisenden Webseite.</li>
						</ul>
						Diese Daten werden lediglich für Zwecke der Datensicherheit, zur Verbesserung unseres Webangebots sowie zur Fehleranalyse auf Grundlage des Art. 6 Abs.1 f DSGVO erhoben. Wir behalten uns das Recht vor, diese Daten im Falle eines Systemmissbrauchs zu verwenden, um die Gründe und den Auslöser des Missbrauchs zu ermitteln, sowie ggf. rechtliche Schritte einzuleiten. Im Übrigen wird die IP-Adresse Ihres Rechners lediglich anonymisiert (verkürzt um die letzten 6 Ziffern) ausgewertet.
						Sie können unsere Website besuchen ohne Angaben zu Ihrer Person zu machen.
						Wir weisen darauf hin, dass die Datenübertragung im Internet (z.B. bei der Kommunikation per E-Mail) Sicherheitslücken aufweisen kann. Ein lückenloser Schutz der Daten vor dem Zugriff durch Dritte ist nicht möglich. Vertrauliche Daten sollten Sie uns daher auf einen anderen Weg, z.B. per Post zukommen lassen.
						Kontaktformular über die Webseite bzw. E-Mail
						Personenbezogene Daten (z. B. Ihr Name, Adressdaten oder Kontaktdaten), die Sie uns von sich aus, z.B. im Rahmen einer Anfrage oder in sonstiger Weise mitteilen, werden bei uns gespeichert und nur zur Korrespondenz mit Ihnen und nur zu dem Zweck verarbeitet, zu dem Sie uns diese Daten zur Verfügung gestellt haben. Die Verarbeitung dieser Daten erfolgt aufgrund unseres berechtigten Interesses an einer zügigen Beantwortung von Interessenten-Anfragen auf Grundlage des Art 6 Abs.1 lit. f. DSGVO.
						Sichere Datenübertragung
						Um die Sicherheit Ihrer Daten bei der Übertragung zu schützen, verwenden wir ein dem aktuellen Stand der Technik entsprechendes Verschlüsselungsverfahren (SSL) über HTTPS.
						<br><br>
						<b>3. Verwendung von Cookies</b><br>
						In unserem Internetangebot werden sog. Cookies eingesetzt. Cookies sind kleine Textdateien, die von Ihrem Browser gespeichert und auf Ihrem Rechner abgelegt werden. Der Einsatz von Cookies dient dazu, das Internetangebot nutzerfreundlicher zu gestalten. So ist es z.B. möglich den Nutzer für die Dauer der Sitzung wiederzuerkennen, ohne das ständig Nutzername und Kennwort neu eingegeben werden müssen. Die Cookies richten auf Ihrem Rechner keinen Schaden an und werden nach Beendigung Ihrer Sitzung gelöscht. Grundlage für die Datenverarbeitung ist Art. 6 Abs.1 f DSGVO.
						Einige der von uns verwendeten Cookies werden nach dem Schließen Ihres Browsers unmittelbar gelöscht (sog. Sitzungs-Cookies).
						Andere Cookies verbleiben auf Ihrem Endgerät und ermöglichen es, Ihren Browser beim nächsten Besuch wiederzuerkennen (persistente Cookies).
						Die Datenverarbeitung im Zusammenhang mit Cookies, die alleine zur Herstellung der Funktionalität unseres Onlineangebots dienen, erfolgt auf der Grundlage unseres berechtigten Interesses gemäß Art. 6 Abs. 1 lit. f DSGVO.
						Wenn Sie die Verwendung von Cookies nicht wünschen, können Sie Ihren Browser so einstellen, dass eine Speicherung von Cookies nicht akzeptiert wird. Bitte beachten Sie dabei aber, dass Sie in diesem Fall gegebenenfalls nicht sämtliche Funktionen unserer Webseiten nutzen können.
						<br><br>
						<b>4. Ihre Rechte</b><br>
						Nach den Artikeln 15-21 DSGVO können Sie bei Vorliegen der dort beschriebenen Voraussetzungen die nachfolgenden Rechte in Bezug auf die bei uns verarbeiteten personenbezogenen Daten geltend machen.
						Sie können Auskunft gemäß Art. 15 DSGVO über Ihre von uns verarbeitenden personenbezogenen Daten, welche Sie uns mit dem E-Mail-Kontaktformular zugesendet haben, verlangen.
						Sollten unrichtige personenbezogene Daten verarbeitet werden, steht Ihnen gemäß Art. 16 DS-GVO ein Recht auf Berichtigung zu.
						Liegen die gesetzlichen Voraussetzungen vor, so können Sie die Löschung oder Einschränkung der Verarbeitung verlangen (Art. 17, 18 DSGVO).
						Sie haben das Recht, Ihre datenschutzrechtliche Einwilligungserklärung jederzeit zu widerrufen. Durch den Widerruf der Einwilligung wird die Rechtmäßigkeit der aufgrund der Einwilligung bis zum Widerruf erfolgten Verarbeitung nicht berührt.
						Widerspruchsrecht gemäß Art. 21 DSGVO
						Die betroffene Person hat das Recht, aus Gründen, die sich aus ihrer besonderen Situation ergeben, jederzeit gegen die Verarbeitung sie betreffender personenbezogener Daten, die aufgrund von Art. 6 Absatz 1 Buchstaben e) oder f) DSGVO erfolgt, Widerspruch einzulegen; dies gilt auch für ein auf diese Bestimmungen gestütztes Profiling.
						<br><br>
						<b>5. Regelfristen für die Löschung der Daten</b><br>
						Soweit eine gesetzliche Aufbewahrungsvorschrift nicht besteht, werden die Daten gelöscht bzw. vernichtet, wenn sie für die Erreichung des Zwecks der Datenverarbeitung nicht mehr erforderlich sind. Für die Aufbewahrung von personenbezogenen Daten gelten unterschiedliche Fristen, so werden Daten mit steuerrechtlicher Relevanz i.d.R. 10 Jahre, andere Daten nach handelsrechtlichen Vorschriften i.d.R. 6 Jahre aufbewahrt. Schließlich kann sich die Speicherdauer auch nach den gesetzlichen Verjährungsfristen richten, die zum Beispiel nach den §§ 195 ff. des Bürgerlichen Gesetzbuches (BGB) in der Regel drei Jahre, in gewissen Fällen aber auch bis zu dreißig Jahre betragen können.
						<br><br>
						<b>6. Beschwerderecht bei einer Aufsichtsbehörde</b><br>
						Im Falle von Verstößen gegen die DSGVO steht den Betroffenen ein Beschwerderecht bei einer Aufsichtsbehörde, insbesondere in dem Mitgliedstaat ihres gewöhnlichen Aufenthalts, ihres Arbeitsplatzes oder des Orts des mutmaßlichen Verstoßes zu. Das Beschwerderecht besteht unbeschadet anderweitiger verwaltungsrechtlicher oder gerichtlicher Rechtsbehelfe.
						<br><br>
						<b>7. Disclaimer</b><br>
						<br>
						<i>Haftung für Inhalte</i><br>
						Als Diensteanbieter sind wir für eigene Inhalte auf diesen Seiten nach den allgemeinen Gesetzen verantwortlich (gemäß § 7 Abs.1 TMG). Wir sind als Diensteanbieter jedoch nicht verpflichtet, übermittelte oder gespeicherte fremde Informationen zu überwachen oder nach Umständen zu forschen, die auf eine rechtswidrige Tätigkeit hinweisen (§8 bis §10 TMG). Hiervon unberührt bleiben Verpflichtungen zur Entfernung oder Sperrung der Nutzung von Informationen nach den allgemeinen Gesetzen. Eine diesbezügliche Haftung ist jedoch erst ab dem Zeitpunkt der Kenntnis einer konkreten Rechtsverletzung möglich. Bei Bekanntwerden von entsprechenden Rechtsverletzungen werden wir diese Inhalte umgehend entfernen.
						<br><br>
						<i>Haftung für Links</i><br>
						Unser Angebot enthält Links zu externen Websites Dritter, auf deren Inhalte wir keinen Einfluss haben. Deshalb können wir für diese fremden Inhalte auch keine Gewähr übernehmen. Für die Inhalte der verlinkten Seiten ist stets der jeweilige Anbieter oder Betreiber der Seiten verantwortlich. Die verlinkten Seiten wurden zum Zeitpunkt der Verlinkung auf mögliche Rechtsverstöße überprüft. Rechtswidrige Inhalte waren zum Zeitpunkt der Verlinkung nicht erkennbar. Eine permanente inhaltliche Kontrolle der verlinkten Seiten ist jedoch ohne konkrete Anhaltspunkte einer Rechtsverletzung nicht zumutbar. Bei Bekanntwerden von Rechtsverletzungen werden wir derartige Links umgehend entfernen.
					</div>
				</div>
				<div class="modal-footer bg-dark text-light">
					<button type="button" class="btn btn-light btn-default pull-right" data-bs-dismiss="modal"><?php echo $o_glob->GetTranslation('btnClose', 1); ?></button>
				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="CookieConsentModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="accordionCookieConsentInformation" aria-hidden="true">
		<div class="modal-dialog modal-xl">
			<div class="modal-content">
				<div class="modal-header bg-dark text-light text-center">
					<h4 class="modal-title fs-4"><?php echo $o_glob->GetTranslation('DataProtectionNoticeTitle', 1); ?></h4>
				</div>
				<div class="modal-body bg-light-subtle">
					<h5><?php echo $o_glob->GetTranslation('DataProtectionNotice', 1); ?></h5>
					<hr>
					<div class="accordion" id="accordionCookieConsentInformation">
						<div class="accordion-item">
							<h2 class="accordion-header">
								<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseCookieConsentInformation" aria-expanded="false" aria-controls="collapseCookieConsentInformation">
									<?php echo $o_glob->GetTranslation('DataProtection', 1); ?>
								</button>
							</h2>
							<div id="collapseCookieConsentInformation" class="accordion-collapse collapse" data-bs-parent="#accordionCookieConsentInformation">
								<div class="accordion-body" id="cookieConsentContentDataProtection">
									
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer bg-dark text-light">
					<button type="button" class="btn btn-success btn-default pull-right" data-bs-dismiss="modal" id="cookieConsentYes"><?php echo $o_glob->GetTranslation('btnAgreed', 1); ?></button>
				</div>
			</div>
		</div>
	</div>
	<!-- ActivateCookieConsent -->
</body>
</html>