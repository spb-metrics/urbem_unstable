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
    * Página de Filtro - Exportação Arquivos GF
    * Data de Criação   : 08/06/2007
    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Diego Barbosa Victoria
    * @ignore
    $Id: FLManterExportacao.php 63603 2015-09-16 14:54:17Z evandro $
    * Casos de uso: uc-06.03.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TCMBA_MAPEAMENTO.Sessao::getExercicio().'/TTBAConfiguracao.class.php';
include_once CAM_GF_ORC_COMPONENTES.'ITextBoxSelectEntidadeGeral.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterExportacao";
$pgFilt 	= "FL".$stPrograma.".php";
$pgList 	= "LS".$stPrograma.".php";
$pgForm 	= "FM".$stPrograma.".php";
$pgProc 	= "PR".$stPrograma.".php";
$pgOcul 	= "OC".$stPrograma.".php";
$pgJS   	= "JS".$stPrograma.".js";

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
//destroi arrays de sessão que armazenam os dados do FILTRO
Sessao::remove('filtro');

$rsArqExport = $rsAtributos = new RecordSet;

$stAcao = $request->get('stAcao');

$obTMapeamento = new TTBAConfiguracao();
$obTMapeamento->setDado( 'exercicio'    , Sessao::getExercicio() );
$obTMapeamento->setDado( 'cod_entidade' , $stEntidades );
$obTMapeamento->recuperaUnidadeGestoraEntidade( $rsEntidade );
$inCodUnidadeGestora = $rsEntidade->getCampo('cod_unidade_gestora');

if ($inCodUnidadeGestora != "") {
    //Define o objeto da ação stAcao
    $obHdnAcao = new Hidden;
    $obHdnAcao->setName ( "stAcao" );
    $obHdnAcao->setValue( $stAcao );
    
    //Define o objeto que ira armazenar o nome da pagina oculta
    $obHdnAcao = new Hidden;
    $obHdnAcao->setName ( "hdnPaginaExportacao" );
    $obHdnAcao->setValue( "../../../TCMBA/instancias/exportacao/".$pgOcul );
    
    /* Radio para selecionar tipo de exportacao*/
    /* Tipo Arquivo Individual */
    $obRdbTipoExportArqIndividual = new Radio;
    $obRdbTipoExportArqIndividual->setName   ( "stTipoExport"           );
    $obRdbTipoExportArqIndividual->setLabel  ( "Arquivos Individuais"   );
    $obRdbTipoExportArqIndividual->setValue  ( "individuais"            );
    $obRdbTipoExportArqIndividual->setRotulo ( "*Tipo de Exportação"    );
    $obRdbTipoExportArqIndividual->setTitle  ( "Tipo de Exportação"     );
    $obRdbTipoExportArqIndividual->setChecked( true                     );
    /* Tipo Arquivo Compactado */
    $obRdbTipoExportArqCompactado = new Radio;
    $obRdbTipoExportArqCompactado->setName  ( "stTipoExport"    );
    $obRdbTipoExportArqCompactado->setLabel ( "Compactados"     );
    $obRdbTipoExportArqCompactado->setValue ( "compactados"     );
    
    switch ($stAcao) {
        case 'basicos':
            $arNomeArquivos = array( 
                                        "Cargo.txt"
                                        ,"Fonte.txt"
                                        ,"ContaCont.txt"
                                    );
        break;
        case 'programa':
            $arNomeArquivos = array( 
                                        "ProgramaPPA.txt"
                                        ,"IndProg.txt"
                                    );
        break;
        case 'orcamento':
            $arNomeArquivos = array( 
                                        "LimiteCred.txt"
                                        ,"Orgao.txt"
                                        ,"UnidOrca.txt"    
                                        ,"Programa.txt"    
                                        ,"ProjAtv.txt"     
                                        ,"EspRec.txt"      
                                        ,"EspDesp.txt"     
                                        ,"PrevRec.txt"     
                                        ,"Dotacao.txt"     
                                    );
        break;
        case 'consumo':
            $arNomeArquivos = array( 
                                        "EmpreSubven.txt"
                                        ,"Frota.txt"    
                                        ,"Patrimonio.txt"
                                    );
        break;
        case 'consolidados':
            $arNomeArquivos = array( 
                                        "ConsContRazao.txt"
                                        ,"ConsDesExtOrc.txt"
                                        ,"ConsIngExtOrc.txt"
                                        ,"ConsRecOrc.txt"
                                        ,"ConsDespOrc.txt"
                                    );
        break;
        case 'ldo':
            $arNomeArquivos = array( 
                                        "MetasFisicas.txt"
                                        ,"RiscosFiscais.txt"
                                    );
        break;
        case 'programacao':
            $arNomeArquivos = array( 
                                        "CronoDesemb.txt"
                                        ,"MetasArrecada.txt"
                                    );
        break;
        case 'informes':
            $arNomeArquivos = array( 
                                        "Area.txt"  
                                        ,"BenefPen.txt"    
                                        ,"Bolsa.txt"       
                                        ,"Concurso.txt"    
                                        ,"FaixaSalario2.txt"   
                                        ,"Pessoal.txt" 
                                        ,"PreContPessoal.txt" 
                                        ,"ProvApo.txt" 
                                        ,"ResConc.txt" 
                                        ,"Salario2.txt"
                                        ,"AdCont.txt"   
                                        ,"AdConv.txt"   
                                        ,"AltOrc.txt"   
                                        ,"CertCont.txt" 
                                        ,"Combustivel.txt"  
                                        ,"Contrato2.txt"    
                                        ,"Convenio.txt" 
                                        ,"ConvLic.txt"  
                                        ,"Cotacao.txt"  
                                        ,"CPartLic.txt" 
                                        ,"Dispensa.txt"
                                        ,"DocDiver.txt"
                                        ,"DotCont.txt"
                                        ,"DotConv.txt"
                                        ,"EditalComunic.txt"
                                        ,"Empenho.txt"
                                        ,"EstLancExtraOrc.txt"
                                        ,"EstorEmp.txt"
                                        ,"ItemLic.txt"  
                                        ,"Licitaca.txt" 
                                        ,"LiqEmp.txt"   
                                        ,"MovConta.txt" 
                                        ,"MovRestoPagar.txt" 
                                        ,"NotaFisc.txt" 
                                        ,"PagEmp2.txt"
                                        ,"PagRetencao.txt"
                                        ,"PartConv.txt" 
                                        ,"PartLic.txt"  
                                        ,"PubLic.txt"
                                        ,"RecArrec.txt"   
                                        ,"ResCont.txt"
                                        ,"RestoPagar.txt"
                                        ,"Retencao.txt"
                                        ,"RetencaoEmpresa.txt"
                                        ,"Subempenho.txt"
                                        ,"LancExtraOrc.txt"
                                        ,"TermoCont.txt"
                                        ,"EstorRec.txt"
                                        ,"Diarias.txt"
                                        ,"AprevRec.txt"
                                        ,"MovEmp.txt"
                                        ,"EditalCadastro.txt"
                                        ,"EditalEndereco.txt"
                                        ,"PagRetEmpres.txt"
                                    );
        break;
    }
    
    //Manter ordem alfabetica dos arquivos
    sort($arNomeArquivos);
    
    for ($inCounter=0; $inCounter < count($arNomeArquivos); $inCounter++) {
        $arElementosArq[$inCounter]['Arquivo'  ] = $arNomeArquivos[$inCounter];
        $arElementosArq[$inCounter]['Nome'     ] = $arNomeArquivos[$inCounter];
    }
    
    $obPeriodicidade = new Periodicidade();
    $obPeriodicidade->setExercicio      (  Sessao::getExercicio()   );
    $obPeriodicidade->setNull           ( false                     );
    $obPeriodicidade->setValidaExercicio( true                      );
    $obPeriodicidade->setValue          ( 4                         );
    
    $obISelectEntidade = new ITextBoxSelectEntidadeGeral();
    $obISelectEntidade->setCodEntidade(1);
    
    $rsArqSelecionados = new RecordSet;
    $rsArqDisponiveis = new RecordSet;
    $rsArqDisponiveis->preenche($arElementosArq);
    
    $obCmbArquivos = new SelectMultiplo();
    $obCmbArquivos->setName  ( 'arArquivosSelecionados' );
    $obCmbArquivos->setRotulo( "Arquivos"               );
    $obCmbArquivos->setNull  ( false                    );
    $obCmbArquivos->setTitle ( 'Arquivos Disponiveis'   );
    
    // lista de ARQUIVOS disponiveis
    $obCmbArquivos->SetNomeLista1   ( 'arCodArqDisponiveis' );
    $obCmbArquivos->setCampoId1     ( 'Arquivo'             );
    $obCmbArquivos->setCampoDesc1   ( 'Nome'                );
    $obCmbArquivos->SetRecord1($rsArqDisponiveis);
    
    // lista de ARQUIVOS selecionados
    $obCmbArquivos->SetNomeLista2   ( 'arArquivosSelecionados'  );
    $obCmbArquivos->setCampoId2     ( 'Arquivo'                 );
    $obCmbArquivos->setCampoDesc2   ( 'Nome'                    );
    $obCmbArquivos->SetRecord2($rsArqSelecionados);
    
    //Instancia o formulário
    $obForm = new Form;
    $obForm->setAction( "../../../exportacao/instancias/processamento/PRExportador.php" );
    $obForm->setTarget( "telaPrincipal"                                                 ); //oculto - telaPrincipal
    
    //DEFINICAO DO FORMULARIO
    $obFormulario = new Formulario;
    $obFormulario->addForm          ( $obForm                                                               );
    $obFormulario->addTitulo        ( "Dados para geração de arquivos"                                      );
    $obFormulario->addHidden        ( $obHdnAcao                                                            );
    $obFormulario->addComponente    ( $obISelectEntidade                                                    );
    $obFormulario->agrupaComponentes( array($obRdbTipoExportArqIndividual, $obRdbTipoExportArqCompactado)   );
    $obFormulario->addComponente    ( $obPeriodicidade                                                      );
    $obFormulario->addComponente    ( $obCmbArquivos                                                        );
    
    $obFormulario->OK   ();
    $obFormulario->show ();
} else {
    $obLblMensagem = new Label();
    $obLblMensagem->setRotulo   ( "Mensagem"                                                                                                        );
    $obLblMensagem->setValue    ( "Necessário realizar a configuração da Unidade Gestora em :: TCM - BA :: Configuração :: Manter Unidade Gestora." );

    $obFormulario = new Formulario;
    $obFormulario->addTitulo        ( "Informes Mensais", "right"   );
    $obFormulario->addComponente    ( $obLblMensagem                );
    $obFormulario->show ();
}
$jsOnLoad = " jq('#inPeriodicidade option[value=3]').hide(); \n";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
