<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
  * Página Oculta para gerar o arquivo Demostrativo RCL
  * Data de Criação: 20/08/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Arthur Cruz
  *
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
require_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CAM_GPC_TCEMG_NEGOCIO."RTCEMGRelatorioDemostrativoRCL.class.php";

switch ($request->get('stCtrl')) {

    case "preencheSpan":
        
        if ($request->get('stPeriodicidade') == "Mes") {
        
            $obCmbMes = new Select;
            $obCmbMes->setRotulo             ( "Mês"             );
            $obCmbMes->setName               ("cmbPeriodo"       );
            $obCmbMes->setId                 ("cmbPeriodo"       );
            $obCmbMes->addOption             ( "","Selecione"    );
            $obCmbMes->addOption             ( "01", "Janeiro"   );
            $obCmbMes->addOption             ( "02", "Fevereiro" );
            $obCmbMes->addOption             ( "03", "Março"     );
            $obCmbMes->addOption             ( "04", "Abril"     );
            $obCmbMes->addOption             ( "05", "Maio"      );
            $obCmbMes->addOption             ( "06", "Junho"     );
            $obCmbMes->addOption             ( "07", "Julho"     );
            $obCmbMes->addOption             ( "08", "Agosto"    );
            $obCmbMes->addOption             ( "09", "Setembro"  );
            $obCmbMes->addOption             ( "10", "Outubro"   );
            $obCmbMes->addOption             ( "11", "Novembro"  );
            $obCmbMes->addOption             ( "12", "Dezembro"  );
            $obCmbMes->setNull               ( false             );
            $obCmbMes->setStyle              ( "width: 220px"    );
            
            $obFormulario = new Formulario;
            $obFormulario->addComponente ( $obCmbMes );
            
            $obFormulario->montaInnerHTML ();
            $stHTML = $obFormulario->getHTML ();
            
            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );
            $stHTML = str_replace( "\\\\'","\\'",$stHTML );
                       
            $js = "d.getElementById('spnPeriodicidade').innerHTML = '".$stHTML."'";
        } elseif ($request->get('stPeriodicidade' ) == "Bimestre") {

            $obCmbBimestre = new Select;
            $obCmbBimestre->setRotulo        ( "Bimestre"            );
            $obCmbBimestre->setName          ( "cmbPeriodo"          );
            $obCmbBimestre->setId            ( "cmbPeriodo"          );
            $obCmbBimestre->addOption        ( "", "Selecione"       );
            $obCmbBimestre->addOption        ( "1", "1º Bimestre"    );
            $obCmbBimestre->addOption        ( "2", "2º Bimestre"    );
            $obCmbBimestre->addOption        ( "3", "3º Bimestre"    );
            $obCmbBimestre->addOption        ( "4", "4º Bimestre"    );
            $obCmbBimestre->addOption        ( "5", "5º Bimestre"    );
            $obCmbBimestre->addOption        ( "6", "6º Bimestre"    );
            $obCmbBimestre->setNull          ( false                 );
            $obCmbBimestre->setStyle         ( "width: 220px"        );
            
            $obFormulario = new Formulario;
            $obFormulario->addComponente ( $obCmbBimestre );
            
            $obFormulario->montaInnerHTML ();
            $stHTML = $obFormulario->getHTML ();
            
            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );
            $stHTML = str_replace( "\\\\'","\\'",$stHTML );

            $js = "d.getElementById('spnPeriodicidade').innerHTML = '".$stHTML."'";
        } elseif ($request->get('stPeriodicidade' ) == "Semestre") {

            $obCmbSemestre = new Select;
            $obCmbSemestre->setRotulo        ( "Semestre"            );
            $obCmbSemestre->setName          ( "cmbPeriodo"          );
            $obCmbSemestre->addOption        ( "", "Selecione"       );
            $obCmbSemestre->addOption        ( "1", "1º Semestre"    );
            $obCmbSemestre->addOption        ( "2", "2º Semestre"    );
            $obCmbSemestre->setNull          ( false                 );
            $obCmbSemestre->setStyle         ( "width: 220px"        );
            
            $obFormulario = new Formulario;
            $obFormulario->addComponente ( $obCmbSemestre );
            
            $obFormulario->montaInnerHTML ();
            $stHTML = $obFormulario->getHTML ();
            
            $stHTML = str_replace( "\n" ,"" ,$stHTML );
            $stHTML = str_replace( chr(13) ,"<br>" ,$stHTML );
            $stHTML = str_replace( "  " ,"" ,$stHTML );
            $stHTML = str_replace( "'","\\'",$stHTML );
            $stHTML = str_replace( "\\\\'","\\'",$stHTML );
            
            $js = "d.getElementById('spnPeriodicidade').innerHTML = '".$stHTML."'";
        } else {
            $js = "d.getElementById('spnPeriodicidade').innerHTML = ''";
        }
    break;
}

SistemaLegado::executaFrameOculto($js);

?>