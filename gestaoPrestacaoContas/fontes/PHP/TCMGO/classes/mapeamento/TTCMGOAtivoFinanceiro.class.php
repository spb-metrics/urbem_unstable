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
    * Extensão da Classe de mapeamento
    * Data de Criação: 18/04/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Bruce Cruz de Sena

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TTCMGOAtivoFinanceiro.class.php 61525 2015-01-29 19:21:26Z evandro $

    * Casos de uso: uc-06.04.00
*/

include_once( CAM_GF_CONT_MAPEAMENTO."TContabilidadeBalancoFinanceiro.class.php" );

class TTCMGOAtivoFinanceiro extends TContabilidadeBalancoFinanceiro
{

    public function TTCMGOAtivoFinanceiro()
    {
        parent::TContabilidadeBalancoFinanceiro();
        $this->setDado('exercicio', Sessao::getExercicio() );
    }

    public function recuperaArquivoExportacao10(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaArquivoExportacao10",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaArquivoExportacao10()
    {
        $stDataIni = '01/01/'.$this->getDado( 'exercicio' );
        $stDataFim = '31/12/'.$this->getDado( 'exercicio' );
        $stSql = "SELECT
                            *
                            , '' AS brancos
                            , 10 as tipo_registro
                     FROM
                        tcmgo.arquivo_afr_exportacao10( '" .$this->getDado( 'exercicio' ) .  "'
                                                     , 'cod_entidade IN  ( " . $this->getDado ( 'stEntidades' ) ." )
                                                    and tipo <> ''I''
                                                    and cod_estrutural like ''1.%''
                                                    ' ,'$stDataIni','$stDataFim','')
                         as retorno (  cod_estrutural varchar
                                     ,nivel integer
                                     ,nom_conta varchar
                                     ,num_orgao integer
                                     ,num_unidade integer
                                     ,vl_saldo_anterior numeric
                                     ,vl_saldo_debitos  numeric
                                     ,vl_saldo_creditos numeric
                                     ,vl_saldo_atual    numeric
                                     ,nom_sistema varchar
                                     ,tipo_lancamento integer
                                    )
                    where vl_saldo_anterior <> 0
                       or vl_saldo_debitos <> 0
                       or vl_saldo_creditos <> 0
                    ORDER BY cod_estrutural ";

        return $stSql;
    }

    public function recuperaArquivoExportacao11(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaArquivoExportacao11",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaArquivoExportacao11()
    {
        $stDataIni = '01/01/'.$this->getDado( 'exercicio' );
        $stDataFim = '31/12/'.$this->getDado( 'exercicio' );
        $stSql = "SELECT
                            *
                            , 11 as tipo_registro                 
                     FROM
                        tcmgo.arquivo_afr_exportacao11( '" .$this->getDado( 'exercicio' ) .  "'
                                                     , 'cod_entidade IN  ( " . $this->getDado ( 'stEntidades' ) ." )
                                                    and tipo <> ''I''
                                                    and cod_estrutural like ''1.%''
                                                    ' ,'$stDataIni','$stDataFim','')
                         as retorno (  cod_estrutural varchar
                                     ,nivel integer
                                     ,nom_conta varchar
                                     ,num_orgao integer
                                     ,num_unidade integer
                                     ,vl_saldo_anterior numeric
                                     ,vl_saldo_debitos  numeric
                                     ,vl_saldo_creditos numeric
                                     ,vl_saldo_atual    numeric
                                     ,nom_sistema varchar
                                     ,tipo_lancamento integer
                                     ,cod_fonte VARCHAR(13)
                                    )
                    where vl_saldo_anterior <> 0
                       or vl_saldo_debitos <> 0
                       or vl_saldo_creditos <> 0
                    ORDER BY cod_estrutural ";

        return $stSql;
    }

}

?>
