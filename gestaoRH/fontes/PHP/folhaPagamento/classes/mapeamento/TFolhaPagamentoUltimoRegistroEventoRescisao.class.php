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
    * Classe de mapeamento da tabela folhapagamento.ultimo_registro_evento_rescisao
    * Data de Criação: 16/10/2006

    * @author Desenvolvedor: Diego Lemos de Souza

    * Casos de uso: uc-04.05.54

    $Id: TFolhaPagamentoUltimoRegistroEventoRescisao.class.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  folhapagamento.ultimo_registro_evento_rescisao
  * Data de Criação: 16/10/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TFolhaPagamentoUltimoRegistroEventoRescisao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TFolhaPagamentoUltimoRegistroEventoRescisao()
{
    parent::Persistente();
    $this->setTabela("folhapagamento.ultimo_registro_evento_rescisao");

    $this->setCampoCod('');
    $this->setComplementoChave('desdobramento,timestamp,cod_registro,cod_evento');

    $this->AddCampo('desdobramento','char'     ,true  ,'1'  ,true,'TFolhaPagamentoRegistroEventoRescisao');
    $this->AddCampo('timestamp'    ,'timestamp',true  ,''   ,true,'TFolhaPagamentoRegistroEventoRescisao');
    $this->AddCampo('cod_registro' ,'integer'  ,true  ,''   ,true,'TFolhaPagamentoRegistroEventoRescisao');
    $this->AddCampo('cod_evento'   ,'integer'  ,true  ,''   ,true,'TFolhaPagamentoRegistroEventoRescisao');

}

function recuperaRegistrosEventoRescisaoDoContrato(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY ultimo_registro_evento_rescisao.cod_registro ";
    $stSql = $this->montaRecuperaRegistrosEventoRescisaoDoContrato().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRegistrosEventoRescisaoDoContrato()
{
    $stSql .= "SELECT ultimo_registro_evento_rescisao.*                                                      \n";
    $stSql .= "     , registro_evento_rescisao.cod_contrato                                                  \n";
    $stSql .= "  FROM folhapagamento.ultimo_registro_evento_rescisao                                         \n";
    $stSql .= "     , folhapagamento.registro_evento_rescisao                                                \n";
    $stSql .= " WHERE ultimo_registro_evento_rescisao.cod_registro = registro_evento_rescisao.cod_registro     \n";
    $stSql .= "   AND ultimo_registro_evento_rescisao.cod_evento = registro_evento_rescisao.cod_evento         \n";
    $stSql .= "   AND ultimo_registro_evento_rescisao.timestamp = registro_evento_rescisao.timestamp           \n";

    return $stSql;
}

function recuperaContratosParaCalculo(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY cod_contrato ";
    $stSql = $this->montaRecuperaContratosParaCalculo().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContratosParaCalculo()
{
    $stSql .= "  SELECT registro                                                                                            \n";
    $stSql .= "       , contrato.cod_contrato                                                                               \n";
    $stSql .= "       , servidor.numcgm                                                                                     \n";
    $stSql .= "       , (SELECT nom_cgm FROM sw_cgm WHERE numcgm = servidor.numcgm) as nom_cgm                              \n";
    $stSql .= "    FROM folhapagamento.registro_evento_rescisao                                                               \n";
    $stSql .= "       , folhapagamento.ultimo_registro_evento_rescisao                                                        \n";
    $stSql .= "       , pessoal.contrato                                                                                    \n";
    $stSql .= "           , (SELECT servidor_contrato_servidor.cod_contrato                                                 \n";
    $stSql .= "                   , servidor.numcgm                                                                         \n";
    $stSql .= "                FROM pessoal.servidor_contrato_servidor                                                      \n";
    $stSql .= "                   , pessoal.servidor                                                                        \n";
    $stSql .= "               WHERE servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                         \n";
    $stSql .= "               UNION                                                                                         \n";
    $stSql .= "              SELECT contrato_pensionista.cod_contrato                                                       \n";
    $stSql .= "                   , pensionista.numcgm                                                                      \n";
    $stSql .= "                FROM pessoal.contrato_pensionista                                                            \n";
    $stSql .= "                   , pessoal.pensionista                                                                     \n";
    $stSql .= "               WHERE contrato_pensionista.cod_pensionista = pensionista.cod_pensionista                      \n";
    $stSql .= "                 AND contrato_pensionista.cod_contrato_cedente = pensionista.cod_contrato_cedente) as servidor        \n";
    $stSql .= "   WHERE registro_evento_rescisao.cod_registro = ultimo_registro_evento_rescisao.cod_registro                    \n";
    $stSql .= "     AND registro_evento_rescisao.cod_evento   = ultimo_registro_evento_rescisao.cod_evento                      \n";
    $stSql .= "     AND registro_evento_rescisao.desdobramento   = ultimo_registro_evento_rescisao.desdobramento                \n";
    $stSql .= "     AND registro_evento_rescisao.timestamp    = ultimo_registro_evento_rescisao.timestamp                       \n";
    $stSql .= "     AND registro_evento_rescisao.cod_contrato = contrato.cod_contrato                                         \n";
    $stSql .= "     AND contrato.cod_contrato = servidor.cod_contrato                                                       \n";
    $stSql .= "     AND registro_evento_rescisao.cod_periodo_movimentacao = ".$this->getDado("cod_periodo_movimentacao")."    \n";
    $stSql .= "     AND contrato.cod_contrato IN (SELECT cod_contrato                                                   \n";
    $stSql .= "                                         FROM pessoal.contrato_servidor_caso_causa )                         \n";
    $stSql .= "GROUP BY contrato.cod_contrato                                                                               \n";
    $stSql .= "       , contrato.registro                                                                                   \n";
    $stSql .= "       , servidor.numcgm                                                                                     \n";

    return $stSql;
}

function recuperaRegistrosDeEventoSemCalculo(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY ultimo_registro_evento_rescisao.cod_registro ";
    $stSql = $this->montaRecuperaRegistrosDeEventoSemCalculo().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRegistrosDeEventoSemCalculo()
{
    $stSql .= "SELECT ultimo_registro_evento_rescisao.*                                                      \n";
    $stSql .= "     , registro_evento_rescisao.cod_contrato                                                  \n";
    $stSql .= "  FROM folhapagamento.ultimo_registro_evento_rescisao                                         \n";
    $stSql .= "     , folhapagamento.registro_evento_rescisao                                                \n";
    $stSql .= " WHERE ultimo_registro_evento_rescisao.cod_registro = registro_evento_rescisao.cod_registro     \n";
    $stSql .= "   AND ultimo_registro_evento_rescisao.cod_evento = registro_evento_rescisao.cod_evento         \n";
    $stSql .= "   AND ultimo_registro_evento_rescisao.timestamp = registro_evento_rescisao.timestamp           \n";
    $stSql .= "   AND ultimo_registro_evento_rescisao.cod_registro not in (SELECT cod_registro FROM folhapagamento.evento_rescisao_calculado) \n";

    return $stSql;
}

function recuperaContratosDeRegistrosDeEventoSemCalculo(&$rsRecordSet, $stFiltro = "", $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY contrato.registro ";
    $stSql = $this->montaRecuperaContratosDeRegistrosDeEventoSemCalculo().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaContratosDeRegistrosDeEventoSemCalculo()
{
    $stSql .= "SELECT contrato.registro                                                                    \n";
    $stSql .= "     , sw_cgm.numcgm                                                                        \n";
    $stSql .= "     , sw_cgm.nom_cgm                                                                       \n";
    $stSql .= "  FROM folhapagamento.ultimo_registro_evento_rescisao                                         \n";
    $stSql .= "     , folhapagamento.registro_evento_rescisao                                                \n";
    $stSql .= "     , pessoal.contrato                                                                     \n";
    $stSql .= "     , pessoal.servidor_contrato_servidor                                                   \n";
    $stSql .= "     , pessoal.servidor                                                                     \n";
    $stSql .= "     , sw_cgm                                                                               \n";
    $stSql .= " WHERE ultimo_registro_evento_rescisao.cod_registro = registro_evento_rescisao.cod_registro     \n";
    $stSql .= "   AND ultimo_registro_evento_rescisao.cod_evento = registro_evento_rescisao.cod_evento         \n";
    $stSql .= "   AND ultimo_registro_evento_rescisao.timestamp = registro_evento_rescisao.timestamp           \n";
    $stSql .= "   AND registro_evento_rescisao.cod_contrato = contrato.cod_contrato                          \n";
    $stSql .= "   AND contrato.cod_contrato = servidor_contrato_servidor.cod_contrato                      \n";
    $stSql .= "   AND servidor_contrato_servidor.cod_servidor = servidor.cod_servidor                      \n";
    $stSql .= "   AND servidor.numcgm = sw_cgm.numcgm                                                      \n";
    $stSql .= ( $this->getDado("desdobramento")             != "" ) ? " AND ultimo_registro_evento_rescisao.desdobramento      = '".$this->getDado("desdobramento")."'"        : "";
    $stSql .= ( $this->getDado("cod_periodo_movimentacao")  != "" ) ? " AND registro_evento_rescisao.cod_periodo_movimentacao  = ".$this->getDado("cod_periodo_movimentacao")  : "";
    $stSql .= "   AND ultimo_registro_evento_rescisao.cod_registro not in (SELECT cod_registro FROM folhapagamento.evento_rescisao_calculado) \n";
    $stSql .= "GROUP BY contrato.registro, sw_cgm.numcgm, sw_cgm.nom_cgm                                   \n";

    return $stSql;
}

function deletarUltimoRegistroEvento($boTransacao="")
{
    return $this->executaRecupera("montaDeletarUltimoRegistro", $rsRecordSet, "", "", $boTransacao);
}

function montaDeletarUltimoRegistro()
{
    $stSql  = "SELECT criarBufferTexto('stEntidade','".Sessao::getEntidade()."');       \n";
    $stSql .= "SELECT criarBufferTexto('stTipoFolha','R');                              \n";
    $stSql .= "SELECT deletarUltimoRegistroEvento(".$this->getDado("cod_registro")."    \n";
    $stSql .= "                                  ,".$this->getDado("cod_evento")."      \n";
    $stSql .= "                                 ,'".$this->getDado("desdobramento")."'  \n";
    $stSql .= "                                 ,'".$this->getDado("timestamp")."');    \n";

    return $stSql;
}

}
?>
